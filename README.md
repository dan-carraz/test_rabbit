# test_rabbit
Commandes utiles :
```
docker-compose up -d
docker exec -it test_rabbit_php_1 sh
bin/console ra:rpc  product_rpc_consumer #Consumer Serveur RPC
bin/console ra:c -w  product_receiver_guzzle_consumer #Consumer Appel Guzzle
bin/console ra:c -w product_receiver_rpc_consumer #Consumer Appel RPC
