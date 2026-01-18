<?php

return [

    'provider' => 'users', // Varsayılan kullanıcı sağlayıcısı

    'rules' => [
        // Özel doğrulama kuralları eklenebilir
    ],

    'scopes' => [
        // LDAP sorguları için özel kapsamlar eklenebilir
    ],

    'log_auth_attempts' => true, // Giriş denemelerini logla

    'log_auth_successful' => true, // Başarılı girişleri logla

    //'filter' => '(&(objectClass=inetOrgPerson)(uid=:uid))', // LDAP kullanıcı arama filtresi
    'filter' => '(&(objectClass=inetOrgPerson)(mail=:mail))',


    'usernames' => [
        'ldap' => 'uid', // LDAP kimlik doğrulama alanı
        'eloquent' => 'email', // Laravel kullanıcı eşleme alanı
    ],

];
