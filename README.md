**RUSH MVC**  
  
**Authors:**  
Mathilde Wallon  
Marie Parison  
Denis MA  

A completer un jour


**Installation de composer.phar**  
_Dans le shell_

    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    sudo php composer-setup.php  # J'ai du le faire en sudo, mais peut etre pas pour tout le monde
    php -r "unlink('composer-setup.php');"


**Mettre a jour les lib requis dans composer.json**  
_Dans le shell_

    ./composer.phar install
    

**Mettre a jour l'autoload**  
_Dans le shell_

    ./composer.phar dump-autoload
    
    
**Lancer un serveur php**  
- Soit par Apache2 => simulation prod, la racine doit se trouver dans le repertoire de CE fichier
- Soit on monte un serveur spontane, par le binaire php : 
        
       php -S localhost:8000 -d xdebug.remote_enable=1 -d display_errors=1
Cette commande est a taper dans le repertoire de ce fichier)  
le drapeau xdebug.remove_enable=1 active le debugueur.
    
**Convention sur le code**  
A definir, mais on va essayer d'ecrire proprement :)


**Git**  
Chacun sa branche sous cette forme la:   
{user}/{topic}  
ex: marie/database etc.  
Une fois le feature valide, on merge avec le master et on recree une nouvelle branche.  


**Git: ignorer une modification d'un fichier versionne, exemple: la Configuration.php**  
    
    git update-index --skip-worktree App/Config/Configuration.php


**Git Precaution**  
- Essayer au maximum de commit uniquement les lignes qui concernent le commit!
- Nom des commits comprehensible !
- Ne pas faire "git add *" ! :)  
- git add -u   ==> pour indexer les fichiers deja suivis  
- Toujours faire des "git status" avant de commit ! 
- On verra pour le reste


**Mysql**  
Probleme:  
- la base de donnee actuelle, on ne peut pas supprimer un article si il contient des commentaires,  
Pour y remedier, il faut changer la base de donnee mysql de sorte a creer des relation entre tables avec DELETE CASCADE  

Solution :  

    ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_1;
    ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_2;
    ALTER TABLE comments ADD CONSTRAINT comments_ibfk_1 FOREIGN KEY (id_writer) REFERENCES users(id) ON DELETE CASCADE;
    ALTER TABLE comments ADD CONSTRAINT comments_ibfk_2 FOREIGN KEY (id_article) REFERENCES articles(id) ON DELETE CASCADE;

Ce que ca fait :  
Si on supprime un article, ou un user, les commentaires associes seront supprimes juste avant.