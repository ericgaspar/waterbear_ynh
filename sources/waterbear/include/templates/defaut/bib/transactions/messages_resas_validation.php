
<form action="bib.php?module=<?PHP  print($GLOBALS["affiche_page"]["parametres"]["auto_page"]); ?>" method="POST" >
<table width="800px">

<tr>
<td width="500px"><?PHP print(get_intitule("bib/transactions/resas/lettres", "l_panier", array())); ?></td>
<td width="300px">
<div style="width: 100%;">
<input type="text"  id="input_panier" name="input_panier" />
<div id="autocomplete_input_panier"></div>
</div>
</td>
</tr>

<tr>
    <td><?PHP print(get_intitule("bib/transactions/resas/lettres", "l_case_test", array())); ?></td>
    <td><input type="checkbox" name="bool_test" value="1"/></td>
</tr>

<tr>
    <td><?PHP print(get_intitule("bib/transactions/resas/lettres", "l_mail_test", array())); ?></td>
    <td><input type="text" name="mail_test" value=""/></td>
</tr>

<tr>
<td>&nbsp;</td>
<td >
<input type="submit" value="<?PHP print(get_intitule("bib/transactions/resas/lettres", "l_bouton", array())); ?>" />
</td></tr>
</table>
</form>