<?php

namespace App\Shared;
use App\User;
use App\UserRecord;

class URHelper
{
    public static function getTotalMinutes($hour, $minute)
    {
        return $hour * 60 + $minute;
    }

    public static function regUser(
        $persno,
        $nic,
        $oic,
        $staffno,
        $name,
        $ou,
        $comp,
        $persarea,
        $perssubarea,
        $empsgroup,
        $empgroup,
        $psgroup,
        $pslvl,
        $birthdate,
        $email,
        $cellno,
        $reptto,
        $empstats,
        $position,
        $costcentr,
        $upd_sap
    ) {

      //User data for current state employee
      //Use in session handling
        $u = User::find($persno);
        if (!$u) {
            $u = new User;
            $u->id = $persno;
        }

        $u->name        = $name;
        $u->email       = $email;
        $u->staff_no    = $staffno;
        $u->persno      = $persno;
        $u->new_ic      = $nic;
        $u->company_id  = $comp;
        $u->orgunit     = $ou;
        $u->persarea    = $persarea;
        $u->perssubarea = $perssubarea;

        $u->save();

//User records for hsitorical data
        $ur = new UserRecord;
        $ur->user_id      = $persno;
        $ur->new_ic       = $nic;
        $ur->oic          = $oic;
        $ur->staffno      = $staffno;
        $ur->name         = $name;
        $ur->orgunit      = $ou;
        $ur->company_id   = $comp;
        $ur->persarea     = $persarea;
        $ur->perssubarea  = $perssubarea;
        $ur->empsgroup    = $empsgroup;
        $ur->empgroup     = $empgroup;
        $ur->psgroup      = $psgroup;
        $ur->pslvl        = $pslvl;
        $ur->birthdate    = $birthdate;
        $ur->email        = $email;
        $ur->reptto       = $reptto;
        $ur->empstats     = $empstats;
        $ur->position     = $position;
        $ur->costcentr    = $costcentr;
        $ur->upd_sap      = $upd_sap;
        $ur->save();

        return $ur->user_id;

      }
}
