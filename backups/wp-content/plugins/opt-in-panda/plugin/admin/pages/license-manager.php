<?php 
/**
 * License page is a place where a user can check updated and manage the license.
 */
class OPanda_LicenseManagerPage extends OnpLicensing325_LicenseManagerPage  {
 
    public $purchasePrice = '$26';
    
    public function configure() {
        
        if( !current_user_can('administrator') )
            $this->capabilitiy = "manage_opanda_licensing";
                $this->purchasePrice = '$26';
            

        

            $this->menuPostType = OPANDA_POST_TYPE;
        

    }
}

FactoryPages321::register($optinpanda, 'OPanda_LicenseManagerPage');
 