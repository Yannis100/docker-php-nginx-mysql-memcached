En règle générale, c'est plutôt les contenus statiques qui sont mis en cache, par exemple les images, feuilles de style et scripts pour une site web

Dans le cas du database caching, c'est un peu différent

Les bases de données utilisent déjà la mise en cache sur différentes couches (mise en cache des fichiers de données, mise en cache des journaux, mise en cache des tables, ...) pour augmenter les performances.
Dans certaines circonstances, il peut aussi être avantageux pour une application Web de mettre en cache les résultats des requêtes de la base de données.
Par exemple :
Beaucoup de demandes de lecture
Les demandes de lecture nécessitent des requêtes complexes avec beaucoup de jointures



Un cache d'objets en mémoire fournit une table de hachage pour stocker les objets en mémoire (in-memory key-value store).
​	"Objet" désigne données arbitraires.
​	L'accès aux objets se fait à l'aide d'une clé, qui peut également être une donnée arbitraire.
​	Comportement habituel de la mémoire cache :
​		Lorsque la table est pleine, les écritures suivantes entraînent la purge des données plus anciennes dans l'ordre des données les moins utilisées récemment (LRU - Least Recently Used). 
​		Les données ne sont pas conservées sur le disque.
Les caches d'objets sont souvent déployés sur un serveur séparé et accessibles via le réseau.
Pour augmenter la capacité, certaines caches d'objets peuvent être réparties sur plusieurs serveurs.

Memcached est un stockage en mémoire de valeurs-clés pour de petits morceaux de données arbitraires (chaînes de caractères, objets) provenant des résultats d'appels de bases de données, d'appels d'API ou du rendu de pages.



look-aside vs look-through ?



"Thundering herd" : identical cache miss same time

Solution : Le cache distribue les baux (leases) aux instances.
Sur un cache manquant (cache miss), le cache donne un bail à l'instance pour cette clé. L'instance fait une demande de lecture à la base de données et utilise le bail pour créer l'entrée de cache.
Les autres instances n'obtiennent pas de bail. Ils attendent un peu et essaient ensuite de relire le cache.





Il existait avant la version 8 de MySQL un query cache intégré mais il a été supprimé pour diverses raisons (https://www.monsiteestlent.com/base-de-donnees/mysql/mysql-query-cache-ce-quil-faut-savoir)

https://dev.mysql.com/doc/refman/5.7/en/query-cache.html

Plus que le caching de la DB, il faut d'abord la designer de manière optimisée et optimiser les paramètres du DBMS qui fait tourner la DB ainsi que la manière dont elle est utilisée, on trouve pleins d'informations à ce sujet sur le net (https://mariadb.com/kb/en/library/optimization-and-tuning/, https://mariadb.com/kb/en/library/xtradbinnodb-buffer-pool/, https://mariadb.com/kb/en/library/xtradbinnodb-change-buffering/, https://dev.mysql.com/doc/refman/8.0/en/statement-caching.html)



https://mariadb.com/kb/en/library/subquery-cache/



https://dev.mysql.com/doc/refman/8.0/en/buffering-caching.html



MyISAM is designed with the idea that your database is queried far  more than its updated and as a result it performs very fast read  operations. If your read to write(insert|update) ratio is less than 15%  its better to use MyISAM.

InnoDB uses row level locking, has commit, rollback, and  crash-recovery capabilities to protect user data. It supports  transaction and fault tolerance

https://stackoverflow.com/questions/15678406/when-to-use-myisam-and-innodb

https://dev.mysql.com/doc/refman/8.0/en/myisam-key-cache.html

https://mariadb.com/kb/en/library/segmented-key-cache/



aws elasticache

https://aws.amazon.com/fr/elasticache/

https://github.com/mondorobot/terraform-memcached



memcached vs redis

https://docs.aws.amazon.com/fr_fr/AmazonElastiCache/latest/mem-ug/SelectEngine.html





https://pureform.wordpress.com/2008/05/21/using-memcache-with-mysql-and-php/

http://highscalability.com/blog/a-bunch-of-great-strategies-for-using-memcached-and-mysql-be.html

https://en.wikipedia.org/wiki/Database_caching



https://hazelcast.com/use-cases/caching/database-caching/

https://aws.amazon.com/fr/caching/database-caching/

https://redislabs.com/ebook/part-1-getting-started/chapter-2-anatomy-of-a-redis-web-application/2-4-database-row-caching/



slides 83+ L02