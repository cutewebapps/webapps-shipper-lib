<?php


class Shipper_Update extends App_Update {
    /**
     * @var VERSION - Defines the current version of the component.
     */
    const VERSION = '0.1.0';

    /**
     * Updates all of the main tables in the database, updates version
     * upon completion.
     *
     * @return void
     **/
    public function update()
    {
        // table of validating the addresses
        if (!$this->getDbAdapterRead()->hasTable('shipper_validation')) {
            Sys_Io::out('Creating shipper_validation table');
            $this->getDbAdapterWrite()->addTableSql('shipper_validation', '
        
                shv_id             INT NOT NULL AUTO_INCREMENT,
                shv_company        VARCHAR(10)  DEFAULT \'UPS\' NOT NULL,
                shv_hash           VARCHAR(40)  NOT NULL,
                shv_dt             DATETIME     NOT NULL, 
                
                -- initial address
                shv1_zip           VARCHAR(20)  DEFAULT \'\'   NOT NULL,
                shv1_country       CHAR(2)      DEFAULT \'US\' NOT NULL,  -- country of d
                shv1_county        VARCHAR(100) DEFAULT \'\' NOT NULL,    -- county (sometimes required)
                shv1_state         VARCHAR(100) DEFAULT \'\'   NOT NULL,
                shv1_city          VARCHAR(100) DEFAULT \'\'   NOT NULL,
                shv1_addr1         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                shv1_addr2         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                shv1_addr3         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                
                -- address in suggestion
                shv2_zip           VARCHAR(20)  DEFAULT \'\'   NOT NULL,
                shv2_country       CHAR(2)      DEFAULT \'US\' NOT NULL,  -- country of 
                shv2_county        VARCHAR(100) DEFAULT \'\' NOT NULL,    -- county (sometimes required)
                shv2_state         VARCHAR(100) DEFAULT \'\'   NOT NULL,
                shv2_city          VARCHAR(100) DEFAULT \'\'   NOT NULL,
                shv2_addr1         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                shv2_addr2         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                shv2_addr3         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                shv2_zipx4         VARCHAR(10)  DEFAULT \'\'   NOT NULL,
                
                shv_accepted      INT  DEFAULT 0 NOT NULL,
                shv_number        INT  DEFAULT 0 NOT NULL,
                ','shv_id');  
        }
        
        if (!$this->getDbAdapterRead()->hasTable('shipper_account')) {
            Sys_Io::out('Creating shipper_account table');
            $this->getDbAdapterWrite()->addTableSql('shipper_account', '
                shacc_id            INT NOT NULL AUTO_INCREMENT,
                shacc_name          VARCHAR(100) DEFAULT \'\' NOT NULL,  -- shipper account name
                
                shacc_company       VARCHAR(10)  DEFAULT \'\' NOT NULL,  -- shipper company (ups, usps, fedex)
                shacc_enabled       TINYINT(2)   DEFAULT 1 NOT NULL, -- whether account is enabled
                shacc_is_default    TINYINT(2)   DEFAULT 1 NOT NULL, -- whether account is default for the company
                
                shacc_requesterid   VARCHAR(50)  DEFAULT \'\' NOT NULL,
                shacc_accountid     VARCHAR(50)  DEFAULT \'\' NOT NULL,
                shacc_pass          VARCHAR(50)  DEFAULT \'\' NOT NULL,
                shacc_partnerid     VARCHAR(50)  DEFAULT \'\' NOT NULL,
                shacc_licence       VARCHAR(250) DEFAULT \'\' NOT NULL,
                shacc_key           VARCHAR(50)  DEFAULT \'\' NOT NULL,
                shacc_meter         VARCHAR(50)  DEFAULT \'\' NOT NULL,
                
                shacc_test_mode     TINYINT(2) DEFAULT 1 NOT NULL, -- whether account is in test mode
                shacc_insurance     TINYINT(2) DEFAULT 0 NOT NULL, -- whether insurance was enabled
                KEY (shacc_name)
               ','shacc_id');  
        }     

        if (!$this->getDbAdapterRead()->hasTable('shipper_log')) {
            Sys_Io::out('Creating shipper_log table');
            $this->getDbAdapterWrite()->addTableSql('shipper_log', '
                
                shrec_id            INT AUTO_INCREMENT NOT NULL,
                shrec_company       NVARCHAR(30)  DEFAULT \'\' NOT NULL,
                shrec_request_hash  NVARCHAR(40)  DEFAULT \'\' NOT NULL,
                shrec_account_name  VARCHAR(30)   DEFAULT \'default\' NOT NULL,

                shrec_dt            DATETIME      NOT NULL,
                shrec_action        NVARCHAR(20)  DEFAULT \'\' NOT NULL,
                shrec_order_id      VARCHAR(30)   DEFAULT \'\' NOT NULL,
                shrec_result        INT           DEFAULT \'-1\' NOT NULL,
                shrec_request_file  NVARCHAR(100) DEFAULT \'\' NOT NULL,
                shrec_response_file NVARCHAR(100) DEFAULT \'\' NOT NULL,

                shrec_ip            NVARCHAR(20)  DEFAULT \'\' NOT NULL,
               
                KEY i_order_id( shrec_order_id ),
                KEY i_request_hash( shrec_request_hash ), 
                KEY i_shrec_account_name( shrec_account_name )
            ', 'shrec_id' );
        }

        // TODO: shipper_log?
    }
}