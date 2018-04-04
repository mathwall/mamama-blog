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


**Git Precaution**  
- Essayer au maximum de commit uniquement les lignes qui concernent le commit!
- Nom des commits comprehensible !
- Ne pas faire "git add *" ! :)  
- git add -u   ==> pour indexer les fichiers deja suivis  
- Toujours faire des "git status" avant de commit ! 
- On verra pour le reste
