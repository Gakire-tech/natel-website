<?php
// backend/api/config/jwt.php

class JwtHandler {
    
    public static function generateToken($data) {
        $header = json_encode(['typ' => 'JWT', 'alg' => JWT_ALGO]);
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 hours
            'data' => $data
        ]);
        
        $base64Header = self::base64UrlEncode($header);
        $base64Payload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, JWT_SECRET_KEY, true);
        $base64Signature = self::base64UrlEncode($signature);
        
        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }
    
    public static function validateToken($token) {
        $tokenParts = explode('.', $token);
        
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        $header = $tokenParts[0];
        $payload = $tokenParts[1];
        $signature = $tokenParts[2];
        
        $expectedSignature = hash_hmac('sha256', $header . '.' . $payload, JWT_SECRET_KEY, true);
        $expectedSignature = self::base64UrlEncode($expectedSignature);
        
        if (hash_equals($expectedSignature, $signature)) {
            $payloadDecoded = json_decode(self::base64UrlDecode($payload));
            if (isset($payloadDecoded->exp) && $payloadDecoded->exp < time()) {
                return false; // Token expired
            }
            return $payloadDecoded;
        }
        
        return false;
    }
    
    public static function getUserIdFromToken($token) {
        $decoded = self::validateToken($token);
        if ($decoded && isset($decoded->data->id)) {
            return $decoded->data->id;
        }
        return false;
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}