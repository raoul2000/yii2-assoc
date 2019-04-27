<?php

namespace app\components;

class Constant
{
    /**
    * @property boolean when TRUE, the main layout will use a container-fluid CSS class. Otherwise
    * it uses a 'container' CSS class
    */
    const PARAM_FLUID_LAYOUT = 'fluid-layout';
    /**
     * session param name used to store the date range, start and end date.
     */
    const SESS_PARAM_NAME_DATERANGE = 'date_range';
    const SESS_PARAM_NAME_STARTDATE = 'start_date';
    const SESS_PARAM_NAME_ENDDATE = 'end_date';

    /**
     * session param name used to store current Contact info
     */
    const SESS_CONTACT = 'contact';
    /**
     * session param name used to store current Bank Account info
     */    
    const SESS_BANK_ACCOUNT = 'bank_account';
}
