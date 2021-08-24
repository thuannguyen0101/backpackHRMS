<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use myPHPnotes\Microsoft\Auth;
use myPHPnotes\Microsoft\Handlers\Session;
use myPHPnotes\Microsoft\Models\User;


class DemoController
{
    public function Sigin(){
        $tenant = "common";
        $client_id = "5ac622e3-593a-465d-88c0-7aca2fa10962";
        $client_secret = "4Ou~Pm3-M~2krxWGHZ4rMhFHXfSPV~2.Zo";
        $callback = "http://localhost:8000/login/microsoft/success";
        $scopes = ["User.Read"];
        $microsoft = new Auth($tenant, $client_id, $client_secret, $callback, $scopes);
        $microsoft->getAuthUrl();
    }
    public function Callback(){
        $auth = new Auth(Session::get("tenant_id"), Session::get("client_id"), Session::get("client_secret"), Session::get("redirect_uri"), Session::get("scopes"));
        $tokens = $auth->getToken($_REQUEST['code']);
        $accessToken = $tokens->access_token;
        $auth->setAccessToken($accessToken);
        $user = new User;
        echo "Name: "  . $user->data->getDisplayName() . "<br>";
        echo "Email: " . $user->data->getUserPrincipalName() . "<br>";
    }
}
