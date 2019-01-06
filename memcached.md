Le module PHP memcached est en quelque sort l'évolution de memcache afin de palier à certaines limitations de ce dernier, qui n'est plus maintenu. Les auteurs des 2 sont différents mais le "but" est le même, un client pour interagir avec un serveur memcached.





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



Memached est : Un système de mise en cache d'objets de mémoire distribuée de haute performance, de nature générique, mais destiné à être utilisé pour accélérer les applications Web dynamiques en allégeant la charge des bases de données. Memcached crée essentiellement un shard en mémoire sur un pool de serveurs à partir duquel une application peut facilement obtenir et configurer jusqu'à 1MB de données non structurées. Memcached est composé de deux tables de hachage, une du client vers le serveur et une autre à l'intérieur du serveur. La magie est qu'aucun des serveurs memcached n'a besoin de se connaître. Pour augmenter l'échelle, il vous suffit d'ajouter plus de serveurs et l'algorithme de hachage de clé fait tout s'arranger correctement. Memcached n'est pas redondant, n'a pas de basculement ni d'authentification. C'est un serveur simple pour stocker et obtenir des données, les bits complexes doivent être implémentés par des applications.





Cache non déterministe

C'est la façon typique d'utiliser memcached. Je pense que le non-déterminisme entre en jeu parce qu'une application ne peut pas dépendre de la présence de données dans le cache. Les données peuvent avoir été expulsées parce qu'une dalle est pleine ou parce que les données n'ont tout simplement pas encore été ajoutées.



Utile Pour

Idéal pour les objets complexes qui sont lus plusieurs fois. Particulièrement pour les environnements fragmentés dans lesquels vous devez collecter des données à partir de plusieurs fragments.
Bon remplacement pour le cache de requêtes MySQL
Mise en cache des relations et autres listes
Des données lentes qui sont utilisées sur de nombreuses pages
Ne mettez pas en cache si c'est plus pénible à mettre en cache que ce que vous économiserez
Nuages de balises/tags et listes de suggestions automatiques

Par exemple, lorsqu'une photo est téléchargée, elle est téléchargée sur la page de chaque ami. Ces listes sont difficiles à calculer, elles sont donc mises en cache. 







look-aside vs look-through ?



"Thundering herd" : identical cache miss same time

Solution : Le cache distribue les baux (leases) aux instances.
Sur un cache manquant (cache miss), le cache donne un bail à l'instance pour cette clé. L'instance fait une demande de lecture à la base de données et utilise le bail pour créer l'entrée de cache.
Les autres instances n'obtiennent pas de bail. Ils attendent un peu et essaient ensuite de relire le cache.





Étant donné que MySQL a un cache, pourquoi memcached est-il nécessaire ?
Le cache MySQL n'est associé qu'à une seule instance. Ceci limite le cache à l'adresse maximale d'un serveur. Si votre système est plus grand que la mémoire d'un serveur, l'utilisation du cache MySQL ne fonctionnera pas. Et si le même objet est lu depuis une autre instance, il n'est pas mis en cache.
Le cache de requête s'invalide en écriture. Vous construisez toute cette cachette et elle disparaît quand quelqu'un lui écrit. Votre cache n'est peut-être pas vraiment un cache du tout, selon les habitudes d'utilisation.
Le cache de requête est basé sur les lignes. Memcached peut mettre en cache n'importe quel type de données que vous voulez et ne se limite pas à mettre en cache les lignes de la base de données. Memcached peut mettre en cache des objets complexes complexes qui sont directement utilisables sans jointure. 



Mettre en cache tout ce qui est lent à interroger, à récupérer ou à calculer.


C'est la règle de Fotolog de décider quoi mettre en cache. Ce qui est considéré comme lent dépend de vos besoins. Mais quand quelque chose devient lent, c'est un candidat pour la mise en cache.

Typologie des caches de Fotolog


Fotolog a développé une typologie intéressante de leurs différentes stratégies de mise en cache :
Cache non déterministe - le modèle classique de memcached de lecture à travers le cache et d'écriture dans la base de données.
State Cache - maintient l'état actuel de l'application dans le cache.
Cache proactif - Poussez les modifications de la base de données directement dans le cache.
Cache du système de fichiers - enregistrez la charge NFS en servant les fichiers du cache au lieu du système de fichiers.
Cache de page partielle - cache les éléments de page affichables, pas seulement les données.
Réplication basée sur les applications - utilisez une API côté client pour masquer tous les détails de bas niveau de l'interaction avec le cache.







MySQL InnoDB memcached

L'interface Memcached vers InnoDB est une fonctionnalité de MySQL pour supporter le protocole memcached, mais avec InnoDB comme stockage back-end. Il semble que beaucoup de gens ont été confus par ce que cela signifie, alors je vais essayer d'expliquer.

Alors qu'un vrai démon memcached stocke les données en mémoire, MySQL stocke les données de manière persistante dans une table InnoDB. Les applications PHP peuvent lire et écrire des données en utilisant l'extension memcached, comme si elles utilisaient une mémoire memcached standard. Cependant, ils lisent et écrivent vraiment des lignes à partir de la table InnoDB.

C'est un peu plus lent que le memcache standard, parce qu'il a la surcharge d'écriture sur disque est plus grande que l'accès à la mémoire. Mais c'est un peu plus rapide que l'utilisation de SQL pour lire et écrire ces lignes, car cela évite la complexité de l'analyseur SQL et de l'optimiseur de requêtes.

C'est vraiment la nouveauté de MySQL : contourner SQL, et donner accès directement au moteur de stockage InnoDB via une interface simple mais familière. Ils ont choisi memcached sur la théorie que de nombreux développeurs seraient familiers avec ce langage et qu'ils disposent déjà d'outils et d'un support linguistique pour cela.

C'est peut-être un malentendu. Les données ne sont jamais dans Memcached. Il n'y a pas de synchronisation automatique entre MySQL et Memcached. La seule chose est que MySQL imite l'API et le protocole de Memcached. Il n'y a aucune raison qu'ils l'aient fait, si ce n'est pour rendre l'API familière aux développeurs.

Lorsque vous utilisez "Memcached API for InnoDB", vous connectez votre application à un port écouté par le processus démon mysqld. Vos requêtes sur ces connexions lisent et écrivent des lignes directement dans le moteur de stockage InnoDB. Il n'y a pas d'instance Memcached entre les deux.





Divers

Il y a eu quelques suggestions sur l'utilisation de memcached qui ne rentraient dans aucune autre section, donc elles sont rassemblées ici pour la postérité :
Avoir beaucoup de nœuds pour gérer les pertes. La perte d'un nœud avec quelques nœuds provoquera un pic sur la base de données au fur et à mesure que tout se rechargera. Avoir plus de serveurs signifie moins de charge de base de données en cas de panne.
Utilisez une warm standby qui prend en charge l'IP d'un serveur memcached qui tombe en panne. Cela signifie que vos clients n'auront pas à mettre à jour leurs listes de cache.
Memcached peut fonctionner avec UDP et TCP. Les connexions de persistance sont meilleures parce qu'il y a moins de frais généraux. Cache conçu pour utiliser des milliers de connexions.
Utilisez des serveurs memcached séparés pour réduire les conflits avec les applications.
Vérifiez que la taille de vos slab correspondent à la taille des données que vous allouez ou vous pourriez gaspiller beaucoup de mémoire.

Voici quelques stratégies supplémentaires de Memcached et du tutoriel MySQL :
Ne pensez pas à la mise en cache au niveau de la ligne (row) (base de données), pensez à des objets complexes.
N'exécutez pas memcached sur votre serveur de base de données, donnez à votre base de données toute la mémoire disponible.
Ne soyez pas obsédé par la latence TCP - l’hôte TCP/IP localhost est optimisé pour une copie en mémoire.
Pensez multi-get - exécutez les choses en parallèle chaque fois que vous le pouvez.
Toutes les bibliothèques clientes memcached ne sont pas égales, faites quelques recherches sur la vôtre.
Au lieu d'invalider vos données, expirez-les chaque fois que vous le pouvez - memcached fera tout le travail.
Générer des clés intelligentes - par exemple lors de la mise à jour, incrémenter un numéro de version, qui fera partie de la clé.
Pour les points bonus, stockez le numéro de version dans memcached - appelez-le génération
Ce dernier sera bientôt ajouté à Memcached - dès que Brian en aura eu le temps









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



https://github.com/kn007/memcache.php/blob/master/memcache.php





https://lzone.de/cheat-sheet/memcached