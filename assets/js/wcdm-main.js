jQuery(function($){

    class wcdm_delivery_manager_checkout {

        constructor( DD_field ){
            this.__this;
            this.DD_field = DD_field;

            this.eventHandlers();
        }

        eventHandlers(){
            $( document.body ).ready( this.initDateandTimepicker() );
            $( document.body ).on( "change", "input[name='wcdm_delivery_date']", this.update_checkout.bind(this));
        }

        initDateandTimepicker(){
            this.DD_field.flatpickr({ 
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                maxDate: new Date().fp_incr(wcdm_param.wcdm_max_days),
                disable: [
                    function(date) {
                        // Check if the date should be disabled based on wcdm_weekdays
                        if (wcdm_param.wcdm_weekdays) {
                            var weekdaysToDisable = wcdm_param.wcdm_weekdays.split(',').map(Number); // Convert to array of numbers
                            if (weekdaysToDisable.includes(date.getDay())) {
                                return true; // Disable this day if it matches a disabled weekday
                            }
                        }
                        return false; // Enable all other days
                    }
                ],
            });   
        }       

        update_checkout(e){
            $( document.body ).trigger( 'update_checkout' );
        }

    }
    new wcdm_delivery_manager_checkout( $( 'input#datepicker' ) );
});