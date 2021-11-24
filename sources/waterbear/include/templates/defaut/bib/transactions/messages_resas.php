
<?PHP
    $bool_mail=0;
    foreach ($log_mails as $mail){
        $bool_mail=1;
        print ("envoi d'un mail à $mail <br>\n");
    }
    foreach ($mail_erreurs as $erreur) {
        print ("<p style='color:red'>Erreur lors de l'envoi du mail $erreur</p>");
    }
    if ($bool_mail==1) {
        print ("<div style='page-break-after:always;'></div>"); // saut de page
    }
?>
<?PHP print ($message);  ?>

<?PHP  if ($message != "") { ?>
<script language="javascript">
window.print();
</script>
<?PHP } else {
    print("Aucun courrier a imprimer");
}
?>