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
     * session param name used to store current Contact ID and default bank account
     */
    const SESS_PARAM_NAME_CONTACT = 'contact';
    const SESS_PARAM_NAME_CONTACT_ID = 'contact_id';
    const SESS_PARAM_NAME_CONTACT_NAME = 'contact_name';
    const SESS_PARAM_NAME_BANK_ACCOUNT_ID = 'bank_account_id';
    const SESS_PARAM_NAME_BANK_ACCOUNT_NAME = 'bank_account_name';
}
