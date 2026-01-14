Fix invalid cache path after rebuild as were using names paths

You can also use this command 

````
docker exec -it laravel-php bash -lc \
"mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} \
&& chown -R www-data:www-data bootstrap storage \
&& chmod -R ug+rwX bootstrap storage"
`````

NB also run composer install

````
docker network create shared-frontend
````

This makes the necessary cache dirs etc

````
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views,testing,cache/data} && chown -R www-data:www-data bootstrap storage
````
### NPM commands
````
# Run dev build
sail exec node npm run dev
# Run prod build
sail exec node npm run prod
# Install deps
sail exec node npm install

```

