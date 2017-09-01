<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;

use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\Model\ChangePassword;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{

    CONST REQUEST_STATUS_DENIED = "DENIED";
    CONST REQUEST_STATUS_GRANTED = "GRANTED";
    CONST REQUEST_STATUS_INPUT = "INPUTERROR";
    CONST REQUEST_STATUS_ERROR = "ERROR";

    CONST DENIED_REASON_LICENSE = "1";
    CONST DENIED_REASON_TOKEN = "2";
    CONST DENIED_REASON_REFRESH_TOKEN = "3";
    CONST DENIED_REASON_USER = "4";
    CONST DENIED_REASON_DBAL = "5";
    CONST DENIED_REASON_CONTENT = "6";

    CONST ROLE_ADMIN = "ROLE_ADMIN";

    /**
     * @Route("cms/register", name="registerCMSUser")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setActiveUser(true);

            // 4) save the User!
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('home');
        }

        return $this->render(
            ':agriApp:addUser.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("cms/changePasswd", name="changePasswdCMSUser")
     */
    public function changePasswdAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $changePasswordModel = new ChangePassword();
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changePassword = $form->getData();

            //  var_dump($changePassword);

            $password = $passwordEncoder->encodePassword($user, $changePassword->getPlainPassword());

            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render(
            ':agriApp:changePassword.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("api/signup", name="signup")
     */

    public function appUserSignUp(Request $request)
    {
        if ($request->request) {
            $params = array();
            $error_message = array("username" => "", "email" => "");
            $error_input = 0;
            $content = $request->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);

                if (!array_key_exists("challange", $params) ||
                    !array_key_exists("username", $params) ||
                    !array_key_exists("password", $params) ||
                    !array_key_exists("email", $params)) {
                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));
                }

                $challange = $params["challange"];

                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {
                    $response = array();
                    $sublicense = $em->getRepository("AppBundle:SubLicense")->findOneBy(["challange" => $challange]);
                    $registeredTime = $sublicense->getChallangeDate();

                    $timeDiff = time() - (int)$registeredTime; //$date->getTimestamp()
                    if ((($sublicense != null && $sublicense->getSubLicenseAppRole()->getRole() == AppRole::COMPANY_MANAGER) ||
                            ($sublicense != null && $sublicense->getSubLicenseAppRole()->getRole() != AppRole::COMPANY_MANAGER && $sublicense->getSubLicenseBranch() != null)) && $timeDiff < 300) { //

                        $username = $params["username"];
                        $password = $params["password"];
                        $email = $params["email"];

                        $user_check = $em->getRepository("AppBundle:User")->findOneBy(["username" => $username]);

                        if ($user_check != null) {
                            $error_input = 1;
                            $error_message["username"] = "username already exists";
                        }

                        $user_check2 = $em->getRepository("AppBundle:User")->findOneBy(["email" => $email]);

                        if ($user_check2 != null) {
                            $error_input = 1;
                            $error_message["email"] = "email already exists";
                        }

                        $email_error = filter_var($email, FILTER_VALIDATE_EMAIL);

                        if ($email_error == false) {
                            $error_input = 1;
                            $error_message["email"] = "Please enter a valid email";
                        }

                        if ($error_input != 0)
                            return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_INPUT, "message" => $error_message));

                        $user_new = new User();
                        $user_new->setActiveUser(1);
                        $user_new->setEmail($email);
                        $user_new->setPlainPassword("test123"); // for constraint reasons

                        $password_encoded = $this->get('security.password_encoder')->encodePassword($user_new, $password);
                        $user_new->setPassword($password_encoded);
                        $user_new->setUsername($username);
                        $user_new->setAppRole($sublicense->getSubLicenseAppRole());
                        $user_new->setSubLicense($sublicense);
                        $user_new->setRole(User::USER_ROLE_USER);

                        if ($sublicense->getSubLicenseAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                            $user_new->setCompanyBranch($sublicense->getSubLicenseBranch());

                        $sublicense->setSubLicenseUser($user_new);
                        $sublicense->setUsed(1);

                       // $companyExists = $em->getRepository("AppBundle:Company")->findOneBy(["companyLicense" => $sublicense->getLicense()->getId()]);

                       // dump($companyExists);
                      //  exit();
//                        if(null != $companyExists) {
//                            $user_new->setManagedCompany($companyExists);
//                        }

                        $em->persist($user_new);
                        $em->persist($sublicense);

                        $responsea = new Response();
                        $event = new AuthenticationSuccessEvent(array(), $user_new, $responsea);
                        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user_new);
                        $this->get('gesdinet.jwtrefreshtoken.send_token')->attachRefreshToken($event);
                        $refresh_token = $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager')->getLastFromUsername($user_new->getUsername());

                        $em->flush();

                        $em->getConnection()->commit();

                        $response["status"] = "success";
                        $response["token"] = $token;
                        $response["refresh_token"] = $refresh_token->getRefreshToken();
                        //$response["username"] = $user_new->getUsername();
                       // $response["company_exists"] = ($companyExists == null)? false : true;
                        //dump($response);
                        //exit();
                        return new JsonResponse($response);

                    }
                } catch (DBALException $e) {
                    $em->getConnection()->rollBack();

                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_ERROR, "reason" => RegistrationController::DENIED_REASON_DBAL])); //, "what" => $e->getMessage()
                } catch (Exception $e) {
                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_ERROR, "reason" => RegistrationController::DENIED_REASON_CONTENT])); //,
                }
            }
        }
        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_LICENSE]));
    }

    /**
     * @Route("api/license_authentication", name="license_authentication")
     */
    public function licenseAuthentication(Request $request)
    {

        if ($request->request) {
            $params = array();
            $content = $request->getContent();
            if (!empty($content)) {

                try {
                    $params = json_decode($content, true);
                    if (!array_key_exists("license", $params) ||
                        !array_key_exists("timestamp", $params)) {
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));
                    }

                    $license_string = $params["license"];
                    $request_time = $params["timestamp"];
                    $request_time = substr($request_time, 0, -3);
                    $request_time = (int)($request_time);

                    $em = $this->getDoctrine()->getManager();
                    //  $license = $em->getRepository('AppBundle:License')->findOneBy(['license' => $license_string]);
                    $sublicense = $em->getRepository('AppBundle:SubLicense')->findOneBy(['subLicenseString' => $license_string]);

                    $response = array();

                    if ($sublicense != null && $sublicense->getUsed() != 1) { // added the used chck to reject at first page

                        if ($sublicense->getActive() == 1) {
                            if (($sublicense->getSubLicenseBranch() != null && $sublicense->getSubLicenseAppRole()->getRole() != AppRole::COMPANY_MANAGER) ||
                                $sublicense->getSubLicenseAppRole()->getRole() == AppRole::COMPANY_MANAGER) {
                                $parent_license = $sublicense->getLicense();

                                $start_time = strtotime($parent_license->getStartDate()->format('Y-m-d H:i:s'));
                                $expiry_time = strtotime($parent_license->getExpiryDate()->format('Y-m-d H:i:s'));


                                if ($request_time >= $start_time && $request_time <= $expiry_time) {
                                    $response["status"] = RegistrationController::REQUEST_STATUS_GRANTED;

                                    if ($sublicense->getisCompanyManager() == 1)
                                        $response["role"] = AppRole::COMPANY_MANAGER;
                                    else
                                        $response["role"] = (string)$sublicense->getSubLicenseAppRole()->getId();

                                    $response["license"] = $license_string;
                                    $challange = (string)bin2hex(random_bytes(10));
                                    $response["random"] = $challange;
                                    $sublicense->setChallange($challange);
                                    //$date = new DateTime();
                                    $sublicense->setChallangeDate((string)time()); //$date->getTimestamp()
                                    $em->persist($sublicense);
                                    $em->flush();
                                    return new JsonResponse($response);
                                } else {
                                    $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                                    $response["reason"] = RegistrationController::DENIED_REASON_LICENSE;
                                    return new JsonResponse($response);
                                }
                            } else {
                                $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                                $response["reason"] = RegistrationController::DENIED_REASON_CONTENT;
                                return new JsonResponse($response);
                            }
                        } else {
                            $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                            $response["reason"] = RegistrationController::DENIED_REASON_LICENSE;
                            return new JsonResponse($response);
                        }

                    } else {
                        $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                        $response["reason"] = RegistrationController::DENIED_REASON_LICENSE;
                        return new JsonResponse($response);
                    }
                } catch (Exception $e) {
                    $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                    $response["reason"] = RegistrationController::DENIED_REASON_CONTENT;
                    return new JsonResponse($response);

                } catch (DBALException $e) {
                    $response["status"] = RegistrationController::REQUEST_STATUS_DENIED;
                    $response["reason"] = RegistrationController::DENIED_REASON_DBAL;
                    return new JsonResponse($response);
                }
            }
        }
        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_ERROR]));
    }

    /**
     * @Route(path="api/token_authentication", name="token_authentication")
     */
    public function tokenAuthentication(Request $request)
    {
        if ($request->request) {
            $params = array();
            $content = $request->getContent();

            if (!empty($content)) {
                try {
                    $params = json_decode($content, true); // 2nd param to get as array

                    if (!array_key_exists("username", $params) ||
                        !array_key_exists("password", $params) ||
                        !array_key_exists("timestamp", $params)) {
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));
                    }

                    $username = $params["username"];//$request->request->get('username');
                    $password = $params["password"];//$request->request->get('password');
                    $timestamp = $params["timestamp"];


                    $request_time = substr($timestamp, 0, -3);
                    $request_time = (int)($request_time);

                    if (time() - $request_time > 300)
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));

                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $username, "activeUser" => 1]);

                    if (!$user) {
                        //throw $this->createNotFoundException();
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_USER]));
                    }


                    $license = $user->getSubLicense()->getLicense();

                    $start_time = strtotime($license->getStartDate()->format('Y-m-d H:i:s'));
                    $expiry_time = strtotime($license->getExpiryDate()->format('Y-m-d H:i:s'));


                    if ($request_time < $start_time && $request_time > $expiry_time)
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_LICENSE]));

                    if ($license->getActive() == 0)
                        throw new Exception("License Not Active", 666);

                    // password check
                    if (!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
                        throw new Exception("Invalid Username or Password");
                    }

                    // Use LexikJWTAuthenticationBundle to create JWT token that hold only information about user name
                    $responsea = new Response();
                    $event = new AuthenticationSuccessEvent(array(), $user, $responsea);
                    $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
                    $this->get('gesdinet.jwtrefreshtoken.send_token')->attachRefreshToken($event);
                    $refresh_token = $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager')->getLastFromUsername($user->getUsername());

                    $appRole = $user->getAppRole()->getRole();

                    if ($token == null)
                        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_TOKEN]));

                    // Return genereted tocken
                    return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_GRANTED, "token" => $token, "refresh_token" => $refresh_token->getRefreshToken(), "role" => $appRole));
                } catch (Exception $e) {
                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));

                } catch (DBALException $e) {
                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_DBAL]));
                }
            }
        }
        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));
    }

    /**
     * @Route(path="api/token_refresh", name="token_refresh")
     */
    public function tokenRefresh(Request $request)
    {
        $content = $request->getContent();

        if (!empty($content)) {
            $em = $this->getDoctrine()->getManager();
            $params = json_decode($content, true);
            $refresh_token = $params["refresh_token"];
            $timestamp = $params["timestamp"];

            $request_time = substr($timestamp, 0, -3);
            $request_time = (int)($request_time);

            if (time() - $request_time > 300)
                return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));

            $ref_token_obj = $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager')->get($refresh_token);

            if (null === $ref_token_obj || !$ref_token_obj->isValid())
                return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_REFRESH_TOKEN]));

            $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $ref_token_obj->getUsername(), "activeUser" => 1]);

            if (null === $user) {
                return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_USER));
            }
            try {

                $license = $user->getSubLicense()->getLicense();

                $start_time = strtotime($license->getStartDate()->format('Y-m-d H:i:s'));
                $expiry_time = strtotime($license->getExpiryDate()->format('Y-m-d H:i:s'));


                if ($request_time < $start_time || $request_time > $expiry_time)
                    return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_LICENSE]));

                $new_expiry = new \DateTime();
                $new_expiry->modify('+2592000 seconds'); //One day
                $ref_token_obj->setValid($new_expiry);
                $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager')->save($ref_token_obj);
                $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
                $appRole = $user->getAppRole()->getRole();

                return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_GRANTED, "token" => $token, "refresh_token" => $ref_token_obj->getRefreshToken(), "role" => $appRole));
            } catch (DBALException $e) {
                return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_ERROR, "reason" => RegistrationController::DENIED_REASON_DBAL));
            } catch (Exception $e) {
                return new JsonResponse(array("status" => RegistrationController::REQUEST_STATUS_ERROR, "reason" => RegistrationController::DENIED_REASON_LICENSE));
            }
        }
        return new JsonResponse(array(["status" => RegistrationController::REQUEST_STATUS_DENIED, "reason" => RegistrationController::DENIED_REASON_CONTENT]));
    }
}