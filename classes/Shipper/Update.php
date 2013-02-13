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
                shv1_addr2         VARCHAR(35) DEFAULT \'\'   NOT NULL,
                
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
                snv_number        INT  DEFAULT 0 NOT NULL,
                ','shv_id');  
            $this->_msg( 'Shipper Validation Log Was Created' );
        }
        // TODO: shipper labels, shoppier label items

    }
}