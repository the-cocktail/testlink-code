README The Cocktail
===================

En este _README_ dejo documentado como he logrado actualizar el código que
corre detrás de _testlink.the-cocktail.com_ actualmente alojado en Google:

 * 1º.- He conseguido acceso por SSH a la máquina virtual y mi usuario tiene
permiso de _sudo_ en la misma (añado su IP en mi /etc/hosts como _testlink_).

 * 2º.- Desde el repo en Github (http://github.com/the-cocktail/testlink-code)
en la rama de la versión que tenemos actualmente instalada (_the-cocktail_1_9_19_)
y me descargo su ZIP (_testlink-code-the-cocktail_1_9_19.zip_).

 * 3º. Subo el ZIP al servidor:

    > scp Downloads/testlink-code-the-cocktail_1_9_19.zip testlink:

 * 4º.- En mi _home_ dentro de la máquina tengo un enlace simbólico a la carpeta
_htdocs_ de _testlink_ con el nombre *testlink_htdocs*. Entro en la máquina y
descomprimo el ZIP:

```Shell
> ssh testlink
Linux testlink-1-vm [...]

The programs included with the Debian GNU/Linux system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Debian GNU/Linux comes with ABSOLUTELY NO WARRANTY, to the extent
permitted by applicable law.
       ___ _ _                   _
      | _ |_) |_ _ _  __ _ _ __ (_)
      | _ \ |  _| ' \/ _` | '  \| |
      |___/_|\__|_|_|\__,_|_|_|_|_|

  *** Welcome to the Bitnami TestLink 1.9.19-2 ***
  *** Documentation:  https://docs.bitnami.com/google/apps/testlink/ ***
  ***                 https://docs.bitnami.com/google/ ***
  *** Bitnami Forums: https://community.bitnami.com/ ***
nando@testlink-1-vm:~$ unzip testlink-code-the-cocktail_1_9_19.zip
```

 * 5º- Y por último **copio a mano la carpeta de templates, imágenes y cualquier otro fichero modificado**:

```Shell
sudo cp testlink-code-the-cocktail_1_9_19/config.inc.php testlink_htdocs/
sudo cp -Rf testlink-code-the-cocktail_1_9_19/gui/templates/* testlink_htdocs/gui/templates/
sudo cp -Rf testlink-code-the-cocktail_1_9_19/gui/themes/default/* testlink_htdocs/gui/themes/default/
sudo cp -r testlink-code-the-cocktail_1_9_19/gui/themes/default/images/* testlink_htdocs/gui/themes/default/images/
sudo cp testlink-code-the-cocktail_1_9_19/lib/functions/print.inc.php testlink_htdocs/lib/functions/
sudo cp testlink-code-the-cocktail_1_9_19/locale/es_ES/strings.txt testlink_htdocs/locale/es_ES
sudo cp testlink-code-the-cocktail_1_9_19/third_party/ext-js/css/ext-all.css testlink_htdocs/third_party/ext-js/css/
sudo cp testlink-code-the-cocktail_1_9_19/gui/templates/login-model-marcobiedermann.tpl testlink_htdocs/gui/templates/login-model-marcobiedermann.tpl
sudo cp testlink-code-the-cocktail_1_9_19/gui/themes/default/login/codepen.io/marcobiedermann/css/style.css testlink_htdocs/gui/themes/default/login/codepen.io/marcobiedermann/css
```
