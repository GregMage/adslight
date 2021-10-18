<{if $categoriesRows > 0}>
    <div class="outer">
        <form name="select" action="categories.php?op=" method="POST"
              onsubmit="if(window.document.select.op.value =='') {return false;} else if (window.document.select.op.value =='delete') {return deleteSubmitValid('categoriesId[]');} else if (isOneChecked('categoriesId[]')) {return true;} else {alert('<{$smarty.const.AM_ADSLIGHT_SELECTED_ERROR}>'); return false;}">
            <input type="hidden" name="confirm" value="1">
            <div class="floatleft">
                <select name="op">
                    <option value=""><{$smarty.const.AM_ADSLIGHT_SELECT}></option>
                    <option value="delete"><{$smarty.const.AM_ADSLIGHT_SELECTED_DELETE}></option>
                </select>
                <input id="submitUp" class="formButton" type="submit" name="submitselect" value="<{$smarty.const._SUBMIT}>" title="<{$smarty.const._SUBMIT}>">
            </div>
            <div class="floatcenter0">
                <div id="pagenav"><{$pagenav|default:''}></div>
            </div>

            <!-- pager -->
            <div id="pager" class="pager">
                <form>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/first.png" class="first"/>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/prev.png" class="prev"/>
                    <!-- the "pagedisplay" can be any element, including an input -->
                    <span class="pagedisplay" data-pager-output-filtered="{startRow:input} &ndash; {endRow} / {filteredRows} of {totalRows} total rows"></span>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/next.png" class="next"/>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/last.png" class="last"/>
                    <select class="pagesize">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="all">All Rows</option>
                    </select>
                </form>
            </div>

            <table id="sortTable" class="tablesorter-blue" cellspacing="1" cellpadding="0" width="100%">
                <thead>
                <tr>
                    <th align="center" width="5%"><input name="allbox" title="allbox" id="allbox" onclick="xoopsCheckAll('select', 'allbox');" type="checkbox" title="Check All" value="Check All"></th>
                    <th class="left"><{$selectorcid}></th>
                    <th class="left"><{$selectorpid}></th>
                    <th class="left"><{$selectortitle}></th>
                    <th class="left"><{$selectorcat_desc}></th>
                    <th class="left"><{$selectorcat_keywords}></th>
                    <th class="left"><{$selectorimg}></th>
                    <th class="left"><{$selectorcat_order}></th>
                    <th class="left"><{$selectoraffprice}></th>
                    <th class="left"><{$selectorcat_moderate}></th>
                    <th class="left"><{$selectormoderate_subcat}></th>

                    <th class="center width5"><{$smarty.const.AM_ADSLIGHT_FORM_ACTION}></th>
                </tr>
                </thead>

                <tbody>
                <{foreach item=categoriesArray from=$categoriesArrays}>
                    <tr class="<{cycle values="odd,even"}>">

                        <td align="center" style="vertical-align:middle;"><input type="checkbox" name="categories_id[]" title="categories_id[]" id="categories_id[]" value="<{$categoriesArray.categories_id|default:''}>"></td>
                        <td class='left'><{$categoriesArray.cid}></td>
                        <td class='left'><{$categoriesArray.pid}></td>
                        <td class='left'><{$categoriesArray.title}></td>
                        <td class='left'><{$categoriesArray.cat_desc}></td>
                        <td class='left'><{$categoriesArray.cat_keywords}></td>
                        <td class='left'><{$categoriesArray.img}></td>
                        <td class='left'><{$categoriesArray.cat_order}></td>
                        <td class='left'><{$categoriesArray.affprice}></td>
                        <td class='left'><{$categoriesArray.cat_moderate}></td>
                        <td class='left'><{$categoriesArray.moderate_subcat}></td>
                        <td class="center width5"><{$categoriesArray.edit_delete}></td>
                    </tr>
                <{/foreach}>


                </tbody>
            </table>


            <!-- pager -->
            <div id="pager" class="pager">
                <form>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/first.png" class="first"/>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/prev.png" class="prev"/>
                    <!-- the "pagedisplay" can be any element, including an input -->
                    <span class="pagedisplay" data-pager-output-filtered="{startRow:input} &ndash; {endRow} / {filteredRows} of {totalRows} total rows"></span>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/next.png" class="next"/>
                    <img src="<{$mod_url}>/assets/js/tablesorter/css/images/last.png" class="last"/>
                    <select class="pagesize">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="all">All Rows</option>
                    </select>
                </form>
            </div>


            <br>
            <br>
            <{else}>
            <table id="sortTable" class="tablesorter-blue" cellspacing="1" width="100%" cellspacing="1" class="outer">
                <tr>

                    <th align="center" width="5%"><input name="allbox" title="allbox" id="allbox" onclick="xoopsCheckAll('select', 'allbox');" type="checkbox" title="Check All" value="Check All"></th>
                    <th class="left"><{$selectorcid}></th>
                    <th class="left"><{$selectorpid}></th>
                    <th class="left"><{$selectortitle}></th>
                    <th class="left"><{$selectorcat_desc}></th>
                    <th class="left"><{$selectorcat_keywords}></th>
                    <th class="left"><{$selectorimg}></th>
                    <th class="left"><{$selectorcat_order}></th>
                    <th class="left"><{$selectoraffprice}></th>
                    <th class="left"><{$selectorcat_moderate}></th>
                    <th class="left"><{$selectormoderate_subcat}></th>

                    <th class="center width5"><{$smarty.const.AM_ADSLIGHT_FORM_ACTION}></th>
                </tr>
                <tr>
                    <td class="errorMsg" colspan="11">There are no $categories</td>
                </tr>
            </table>
    </div>
    <br>
    <br>
<{/if}>
