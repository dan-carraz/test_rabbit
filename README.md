# test_rabbit
Commandes utiles :
```
docker-compose up -d
docker exec -it test_rabbit_php_1 sh
bin/console ra:rpc  product_rpc_consumer #Consumer RPC
bin/console ra:c -w product_receiver_consumer #Consumer classique
