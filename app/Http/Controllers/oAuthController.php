<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class oAuthController extends Controller {
    public function bitrix(Request $request) {
        //$clearPortal = explode("/",explode("//", $request->portal)[1])[0];
        $clearPortal = "monkey.bitrix24.ru";
        $clientId = "local.6621555f3ead38.99934649";
        return redirect()->to("https://$clearPortal/oauth/authorize/?client_id=$clientId");
    }
}
