{*
Testlink Open Source Project - http://testlink.sourceforge.net/

title bar + menu

@filesource navBar.tpl
@internal revisions
@since 1.9.7

*}
{lang_get var="labels"
          s="title_events,event_viewer,home,testproject,title_specification,title_execute,
             title_edit_personal_data,th_tcid,link_logout,title_admin,
             search_testcase,title_results,title_user_mgmt"}
{$cfg_section=$smarty.template|replace:".tpl":""}
{config_load file="input_dimensions.conf" section=$cfg_section}

{include file="inc_head.tpl" openHead="yes"}
</head>
<body style="min-width: 800px;">

<div class="menu_title">
  <span><a href='lib/usermanagement/userInfo.php' target="mainframe" accesskey="i"
          tabindex="6"><img src="{$tlImages.account}" title="{$labels.title_edit_personal_data}"></a>
        <a href="logout.php?viewer={$gui->viewer}" target="_parent" accesskey="q">
        <img src="{$tlImages.logout}" title="{$labels.link_logout}"></a>
  </span>
  <span>{$gui->whoami|escape}</span>
</div>

<div style="width: 100%; padding: 4px; 4px; 2px; 4px;">
  <a href="index.php" target="_parent">
    <img alt="The Cocktail Logo" title="logo" src="{$smarty.const.TL_THEME_IMG_DIR}{$tlCfg->logo_navbar}" />
  </a>
</div>

<div style="clear: both"></div>
<div class="menu_bar" style="margin: 0px 0px 0px 0px;">
  {if $gui->tprojectID}
    <div style="float: right; padding-left: 10px;">
    {if $gui->grants->view_testcase_spec == "yes"}
      <form style="display:inline" target="mainframe" name="searchTC" id="searchTC"
            action="lib/testcases/archiveData.php" method="get">
      <input style="font-size: 80%; position:relative; top:-1px;" type="text" size="{$gui->searchSize}"
             title="{$labels.search_testcase}" name="targetTestCase" value="{$gui->tcasePrefix}" />

        {* useful to avoid a call to method to get test case prefix in called page *}
      <input type="hidden" id="tcasePrefix" name="tcasePrefix" value="{$gui->tcasePrefix}" />

        {* Give a hint to archiveData, will make logic simpler to understand *}
      <input type="hidden" id="caller" name="caller" value="navBar" />
      <img src="{$tlImages.magnifier}"
           title="{$labels.search_testcase}" alt="{$labels.search_testcase}"
           onclick="document.getElementById('searchTC').submit()" class="clickable"
           style="position:relative; top:2px;" />
      <input type="hidden" name="edit" value="testcase"/>
      <input type="hidden" name="allow_edit" value="0"/>
      </form>
    {/if}
    </div>
  {/if}

  {if $gui->TestProjects != ""}
    <div style="float: right;">
      <form style="display:inline" name="productForm" action="lib/general/navBar.php?viewer={$gui->viewer}" method="get">
        Proyecto:
        <select style="font-size: 80%;position:relative; top:-1px;" name="testproject" onchange="this.form.submit();">
            {foreach key=tproject_id item=tproject_name from=$gui->TestProjects}
            <option value="{$tproject_id}" title="{$tproject_name|escape}"
              {if $tproject_id == $gui->tprojectID} selected="selected" {/if}>
              {$tproject_name|truncate:#TESTPROJECT_TRUNCATE_SIZE#|escape}</option>
          {/foreach}
        </select>
      </form>
    </div>
  {/if}

  {$session.testprojectTopMenu}

</div>






{if $gui->updateMainPage == 1}
  <script type="text/javascript">
  parent.mainframe.location = "{$basehref}lib/general/mainPage.php";
  </script>
{/if}

</body>
</html>
