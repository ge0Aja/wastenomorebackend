<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 10/11/17
 * Time: 7:57 PM
 */

namespace AppBundle\Composer;


use Composer\Script\Event;

class HerokuEnvironment
{
    /**
     * Populate Heroku environment
     *
     * @param Event $event Event
     */
    public static function populateEnvironment(Event $event)
    {
        $url = getenv('CLEARDB_DATABASE_URL'); // If MySQL is chosen
        // $url = getenv('HEROKU_POSTGRESQL_IVORY_URL'); If PostgreSQL is chosen

        if ($url) {
            $url = parse_url($url);
            putenv("database_host={$url['host']}");
            putenv("database_user={$url['user']}");
            putenv("database_password={$url['pass']}");
            putenv("database_port=null");
//            putenv("doctrn_ca=/app/var/doctrn/cleardb-ca.pem");
//            putenv("doctrn_certificate=/app/var/doctrn/bfb8330e3ead61-cert.pem");
//            putenv("doctrn_private_key=/app/var/doctrn/bfb8330e3ead61-key.pem");


            $db = substr($url['path'], 1);
            putenv("database_name={$db}");
        }

        $io = $event->getIO();
        $io->write('CLEARDB_DATABASE_URL=' . getenv('CLEARDB_DATABASE_URL'));
        $io->write('database_host=' . getenv('database_host'));
        $io->write('database_user=' . getenv('database_user'));
        $io->write('database_password=' . getenv('database_password'));
        $io->write('database_port=' . getenv('database_port'));
    }
}