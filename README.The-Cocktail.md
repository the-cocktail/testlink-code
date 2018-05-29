README The Cocktail
===================

En este _README_ dejo documentado como he logrado actualizar el código que
corre detrás de _testlink.the-cocktail.com_:

 * 1º.- He conseguido acceso por SSH a la máquina virtual y mi usuario tiene
permiso de _sudo_ en la misma (meto su IP en mi /etc/hosts como _testlink_).

 * 2º.- Desde el repo en Github (http://github.com/the-cocktail/testlink-code)
me bajo un ZIP desde la rama con el código que quiero desplegar (estoy usando
la rama *testlink_1_9*).

 * 3ro. Subo el ZIP al servidor:

    > scp Downloads/testlink-code-testlink_1_9.zip testlink:

 * 4º.- Dentro de la máquina tengo un enlace simbólico a la carpeta _htdocs_ 
de _testlink_ con el nombre *testlink_htdocs*. Entro y descomprimo el ZIP en
la carpeta donde tengo dicho enlace simbólicó (mi _home_ en este caso, para
no dar vueltas;):

    > ssh testlink
    *** Welcome to the Bitnami TestLink 1.9.16-3 ***
    *** Documentation:  https://docs.bitnami.com/google/apps/testlink/ ***
    ***                 https://docs.bitnami.com/google/ ***
    *** Bitnami Forums: https://community.bitnami.com/ ***
    nando@tck-testlink1-vm:~$ unzip testlink-code-testlink_1_9.zip

 * 5º- Y por último copio a mano los ficheros que he modificado, guardando
previamente los ficheros actuales con extensión _.ORIG_: 

    > cp testlink_htdocs/config.inc.php testlink_htdocs/config.inc.php.ORIG
    > cp testlink-code-testlink_1_9/config.inc.php testlink_htdocs/
    > cp testlink_htdocs/gui/themes/default/css/testlink.css testlink_htdocs/gui/themes/default/css/testlink.css.ORIG
    > cp testlink-code-testlink_1_9/gui/themes/default/css/testlink.css testlink_htdocs/gui/themes/default/css/testlink.css
    > cp testlink_htdocs/gui/themes/default/css/tl_documents.css testlink_htdocs/gui/themes/default/css/tl_documents.css.ORIG
    > cp testlink_code-testlink_1_9/gui/themes/default/css/tl_documents.css testlink_htdocs/gui/themes/default/css/tl_documents.css.ORIG
