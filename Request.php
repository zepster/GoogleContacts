<?php
namespace GoogleContacts\Request;

class Request { 
    
    public static function req($opt) {
        //TODO curl_setopt_array() + default settings merge
        $ch = curl_init($opt['url']);
        if ($opt['method'] === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opt['data']));
        }
            
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $opt['headers']);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        
        if (!$response) {
            throw new \Exception('Request failed '. curl_error($ch));
        }
        
        curl_close($ch);
        
        return ['response' => $response, 'status' => $status];
    }
    
}

