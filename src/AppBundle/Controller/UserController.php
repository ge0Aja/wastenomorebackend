<?php

namespace AppBundle\Controller;

use AppBundle\Entity\License;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/cmsUsersRecords", name="cmsUsersRecords")
     */
    public function getCmsUsersRecords()
    {

        $em = $this->getDoctrine()->getManager();
        $criteria = array('role' => RegistrationController::ROLE_ADMIN);
        $cmsUsers = $em->getRepository('AppBundle:User')->findBy($criteria);
        return $this->render("agriApp/Users/cmsUsersInJson.html.twig", ['cmsUsersRecords' => $cmsUsers]);
    }

    /**
     * @Route("/appUsersRecords", name="appUsersRecords")
     */
    public function getAppUsersRecords()
    {

        $em = $this->getDoctrine()->getManager();
        $criteria = array('role' => RegistrationController::ROLE_USER);
        $appUsers = $em->getRepository('AppBundle:User')->findBy($criteria);
        return $this->render("agriApp/Users/appUsersInJson.html.twig", ['appUsersRecords' => $appUsers]);
    }

    /**
     * @Route("/cmsUsers", name="cmsUsers")
     */
    public function CmsUsers()
    {
        return $this->render(":agriApp/Users:cmsUsers.html.twig");
    }


    /**
     * @Route("/appUsers", name="appUsers")
     */
    public function AppUsers()
    {
        $em = $this->getDoctrine()->getManager();
        $Roles = $em->getRepository('AppBundle:AppRole')->findAll();
        $Licenses = $em->getRepository('AppBundle:License')->findAll();
        $Branches = $em->getRepository('AppBundle:Branch')->findAll();
        return $this->render(":agriApp/Users:appUsers.html.twig",["Licenses" => $Licenses, "Roles" => $Roles, "Branches" => $Branches]);
    }


    /**
     * @Route("/resetPasswordAppUser", name="resetPasswordAppUser")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getMethod() == 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                try {
                    $id = $request->request->get('h_resetpassworduserid');
                    $user = $em->getRepository('AppBundle:User')->find($id);
                    if($user->getRole() == RegistrationController::ROLE_USER) {
                        $password = $passwordEncoder->encodePassword($user, $request->request->get('plainpasswordreset'));
                        $user->setPassword($password);
                        $em->persist($user);
                        $em->flush();
                        return new JsonResponse(array('status' => 'success'));
                    }
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t reset password', 'info' => $e->getMessage()));
                } catch (Exception $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t reset password', 'info' => $e->getMessage()));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t reset password'));
    }


    /**
     * @Route("/appUserAdd", name="appUserAdd")
     */
    public function addAppUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getMethod() == 'POST') {
            if ($request->request) {

                $em = $this->getDoctrine()->getManager();
                try {
                    $LicenseRecord = $em->getRepository('AppBundle:License')->find($request->request->get('licenseadd'));
                    if($LicenseRecord != null && $LicenseRecord->getLicenseUser()->count() >= $LicenseRecord->getUserCount()) {
                        return new JsonResponse(array("status"=>"invalidLicense", "error" => "No more Users for this License"));
                    }else{

                        $userRecord = new User();
                        $userRecord->setRole(RegistrationController::ROLE_USER);

                        $validator = $this->get('validator');

                        $userRecord->setUsername($request->request->get('usernameadd'));
                        $userRecord->setEmail($request->request->get('emailadd'));

                        $userRecord->setPlainPassword($request->request->get('plainPasswordadd'));
                        $password = $passwordEncoder->encodePassword($userRecord, $request->request->get('plainPasswordadd'));
                        $userRecord->setPassword($password);

                        $userRecord->setAppRole($em->getRepository('AppBundle:AppRole')->find($request->request->get('approleadd')));

                        $companyBranch = $em->getRepository('AppBundle:Branch')->find($request->request->get('branchadd'));
                        $userRecord->setCompanyBranch($companyBranch);

                        if($companyBranch != null && $LicenseRecord == null){
                            return new JsonResponse(array("status"=>"missingLicense", "error" => "You have to select a license"));

                        }
                        $userRecord->setLicense($LicenseRecord);


                        $userRecord->setActiveUser(true);
                        $errors = $validator->validate($userRecord);
                        if (count($errors) > 0) {
                            $str_arr_errors = array();

                            foreach ($errors as $error)
                            {
                                $str_arr_errors[$error->getPropertyPath()] = $error->getMessage();
                            }

                            return new JsonResponse(array("status"=>"invalid", "errors" => $str_arr_errors));
                        }else {
                            $em->persist($userRecord);
                            $em->flush();
                            return new JsonResponse(array('status' => 'success'));
                        }
                    }

                } catch (DBALException $e) {

                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add User', 'info' => $e->getMessage()));

                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add User'));
    }


    /**
     * @Route("/appUserEdit", name="appUserEdit")
     */
    public function editAppUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getMethod() == 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                try {
                    $LicenseRecord = $em->getRepository('AppBundle:License')->find($request->request->get('licenseedit'));
                    if($LicenseRecord != null && $LicenseRecord->getLicenseUser()->count() >= $LicenseRecord->getUserCount()) {
                        return new JsonResponse(array("status"=>"invalidLicense", "error" => "No more Users for this License"));
                    }else{
                        $userRecord = $em->getRepository('AppBundle:User')->find($request->request->get('h_usereditid'));
                        $validator = $this->get('validator');

                        $userRecord->setUsername($request->request->get('usernameedit'));
                        $userRecord->setEmail($request->request->get('emailedit'));

                        $userRecord->setAppRole($em->getRepository('AppBundle:AppRole')->find($request->request->get('approleedit')));

                        $companyBranch = $em->getRepository('AppBundle:Branch')->find($request->request->get('branchedit'));
                        $userRecord->setCompanyBranch($companyBranch);

                        if($companyBranch != null && $LicenseRecord == null){
                            return new JsonResponse(array("status"=>"missingLicense", "error" => "You have to select a license"));

                        }
                        $userRecord->setLicense($LicenseRecord);
                        $userRecord->setPlainPassword('test123');
                        $errors = $validator->validate($userRecord);
                        if (count($errors) > 0) {
                            $str_arr_errors = array();

                            foreach ($errors as $error)
                            {
                                $str_arr_errors[$error->getPropertyPath()] = $error->getMessage();
                            }

                            return new JsonResponse(array("status"=>"invalid", "errors" => $str_arr_errors));
                        }else {
                            $em->persist($userRecord);
                            $em->flush();
                            return new JsonResponse(array('status' => 'success'));
                        }
                    }

                }catch (DBALException $e){

                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit User', 'info' => $e->getMessage()));

                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit User'));
    }


    /**
     * @Route("/appUserDel", name="appUserDel")
     */
    public function deleteAppUser(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                try {
                    $id = $request->request->get('deluserid');
                    $userRecord = $em->getRepository('AppBundle:User')->find($id);
                    if ($userRecord->getRole() == RegistrationController::ROLE_USER) {
                        $em->remove($userRecord);
                        $em->flush();
                        return new JsonResponse(array('status' => 'success'));
                    }

                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete User', 'info' => $e->getMessage()));
                } catch (Exception $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete User', 'info' => $e->getMessage()));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete User'));
    }

    /**
     * @Route("/appUserDeactivate", name="appUserDeactivate")
     */
    public function deactivateUser(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                try {
                    $id = $request->request->get('deactivateuserid');
                    $activateOrDeactivate = $request->request->get('setactive');

                    $activateOrDeactivateBool = $activateOrDeactivate === 'true'? true: false;

                    $userRecord = $em->getRepository('AppBundle:User')->find($id);
                    if ($userRecord->getRole() == RegistrationController::ROLE_USER) {
                       /* var_dump($activateOrDeactivate);
                        exit();*/
                        $userRecord->setActiveUser($activateOrDeactivateBool);
                        $em->persist($userRecord);
                        $em->flush();
                        return new JsonResponse(array('status' => 'success'));
                    }
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t change User status', 'info' => $e->getMessage()));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t change User status'));
    }
}
