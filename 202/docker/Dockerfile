FROM 202ecommerce/prestashop:1.7.8.3

RUN rm -Rf var/www/html/modules/stripe_official/

WORKDIR /var/www/html/modules/stripe_official/

ENTRYPOINT ["sh", "202/docker/entrypoint.sh"]
