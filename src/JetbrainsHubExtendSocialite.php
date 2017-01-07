<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  JetbrainsHubExtendSocialite.php - Part of the socialite-jetbrains-hub project.

  © - Jitesoft 2017
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\Socialite\JetbrainsHub;

use Jitesoft\Socialite\JetbrainsHub\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class JetbrainsHubExtendSocialite {


    public function handle(SocialiteWasCalled $socialiteWasCalled) {
         $socialiteWasCalled->extendSocialite("jetbrains-hub", Provider::class);
    }


}
