<?php

namespace app\components;

trait DateRangeQueryTrait
{
    /**
     * Add a search condition on a single column date being totally or partially included
     * in the current date range.
     * on the `reference_date` attribute.
     *
     * @param string $startDate format yyyy-mm-dd the start date range, or empty if left opened range
     * @param string $endDate format yyyy-mm-dd the start date range, or empty if right opened range
     * @param string $colName default 'reference_date' the name of the date column to apply condition on
     * @return void
     */
    public function dateInRange($startDate, $endDate, $colName = 'reference_date')
    {
        $this->validateDateRangeValues($startDate, $endDate);

        // no date range provided : return the query unchanged
        if (empty($startDate) && empty($endDate)) {
            return $this;
        }

        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');
        if (!empty($startDate) && !empty($endDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['BETWEEN', $colName, $startDate, $endDate]
            ];
        } elseif (!empty($startDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['>=', $colName, $startDate],
            ];
        } elseif (!empty($endDate)) {
            $conditions = [
                'OR',
                ['IS', $colName, $NULL],
                ['<=', $colName, $endDate],
            ];
        }
        return $this->andWhere($conditions);
    }

    /**
     * Add a search condition on the valid_date_start and valid_date_end date columns
     *
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function validInDateRange($startDate, $endDate = null)
    {
        $this->validateDateRangeValues($startDate, $endDate);

        // no date range provided : return the query unchanged
        if (empty($startDate) && empty($endDate)) {
            return $this;
        }

        // create conditions
        $conditions = [];
        $NULL = new \yii\db\Expression('null');

        if (!empty($startDate) && !empty($endDate)) {
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   $endDate
             * ----------|------------|--------------
             *     B     :            :
             *     B     :     E      :       
             *     B     :            :       E
             *           :    B       :       
             *           :    B E     :       
             *           :    B       :       E
             *           :    E       :       
             *           :            :       E
             *           :            :       
             */

            $conditions = [
                'AND',
                ['OR',
                    ['IS', 'valid_date_start', $NULL],
                    ['<=', 'valid_date_start', $endDate],
                ],
                ['OR',
                    ['IS', 'valid_date_end', $NULL],
                    ['>=', 'valid_date_end', $startDate]
                ]
            ];
        } elseif (!empty($startDate)) { // only start date (valid from date ....)
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *       $startDate   
             * ----------|--------------------------
             *     B     :            
             *     B     :     E             
             *           :     E
             *           :                   
             */     
            $conditions = [
                'AND',
                ['OR',
                    ['IS', 'valid_date_start', $NULL],
                    ['<=', 'valid_date_start', $startDate],
                ],
                ['OR',
                    ['IS', 'valid_date_end', $NULL],
                    ['>=', 'valid_date_end', $startDate]
                ]
            ];
        } elseif (!empty($endDate)) { // only end date (valid until date ... )
            /**
             * List of valid dante range configurations
             * ---------------------------------------
             * 
             * B = valid_date_start (Begin)
             * E = valid_date_end   (End)
             * 
             *                    $endDate   
             * ----------------------|------------
             *     B                 :            
             *     B                 :     E             
             *                       :     E
             *                       :                   
             */           
            $conditions = [
                'AND',
                ['OR',
                    ['IS', 'valid_date_start', $NULL],
                    ['<=', 'valid_date_start', $endDate],
                ],
                ['OR',
                    ['IS', 'valid_date_end', $NULL],
                    ['>=', 'valid_date_end', $endDate]
                ]
            ];               

        }
        
        return $this->andWhere($conditions);
    }

    /**
     * Validate the provided date range.
     * If it is a closed range, then start date must NOT be after end date. If that's not the case, throw an exception
     *
     * @param string $startDate format yyyy-mm-dd the start date range, or empty if left opened range
     * @param string $endDate format yyyy-mm-dd the start date range, or empty if right opened range
     * @return void
     */
    private function validateDateRangeValues($startDate, $endDate)
    {
        if (!empty($startDate) && !empty($endDate) && \strtotime($startDate) > strtotime($endDate)) {
            throw new \yii\base\InvalidCallException('start date range must be lower or equal to end date : start = '
                . $startDate . ' end = ' . $endDate);
        }
    }
}
