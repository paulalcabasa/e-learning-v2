<?php
    function load_pic()
    {
        $profile_pic = url('public/images/employee/'.session("employee_no").'.jpg'); 
        if (!$profile_pic) { 
            $profile_pic = url('public/images/profile-avarta@2x.png'); 
        }
        return $profile_pic;
    }
?>