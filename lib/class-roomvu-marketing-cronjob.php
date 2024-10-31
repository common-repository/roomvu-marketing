<?php

class Roomvu_Marketing_Cronjob
{
    protected $cronHookName = 'rvm_cron_activate';

    protected $roomvuService;

    public function __construct($roomvuService)
    {
        $this->roomvuService = $roomvuService;
    }

    /**
     * start cronjob
     *
     * @return void
     */
    public function initCronjob(){
        if ( ! $this->cronHasActive() ) {
            wp_schedule_event( time(), 'hourly', $this->cronHookName );
        }
        add_action( $this->cronHookName, [$this, 'runCronjob'] );

    }

    public function customCronSchedule(){
        $schedules['every_three_minutes'] = array(
            'interval' => 5,
            'display'  => __( 'Every 6 hours' ),
        );
        return $schedules;
    }

    public function cronHasActive(){
        return wp_next_scheduled( $this->cronHookName );
    }

    public function runCronjob(){
		$admins = get_users( array( 'role' => 'administrator' ));
		if(!empty($admins)){
			$currentUserID=wp_get_current_user()->ID;
			wp_set_current_user($admins[0]->ID);
		}
        $this->roomvuService->import();
		if(!empty($admins)){
			wp_set_current_user($currentUserID);
		}

        update_option( 'rvm_cron_text', date( 'Y-m-d H:i:s' ) );
        update_option( 'rvm_cron_text_runs', 'cron_run at' . date( 'Y-m-d H:i:s' ) );
    }

}