openssl genrsa -out "./default.key" 2048
chmod 644 ./default.key
openssl req -new -key "./default.key" -out "./default.csr" -subj "/CN=default/O=default/C=UK"
openssl x509 -req -days 365 -in "./default.csr" -signkey "./default.key" -out "./default.crt"

brew install mkcert
brew install nss   # Firefox support
mkcert -install
mkcert helpdesk.vm medidev.vm api.medidev.vm localhost 127.0.0.1


then add it to nginx config

server {
    listen 443 ssl;
    server_name helpdesk.vm medidev.vm api.medidev.vm;

    ssl_certificate     /etc/nginx/ssl/helpdesk.vm+4.pem;
    ssl_certificate_key /etc/nginx/ssl/helpdesk.vm+4-key.pem;

    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass laravel.test:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS on;
    }
}
