<html>
<head>
<title>Eden admin page</title>
<body>

<table width="100%" border="0">
    <tr>
        <td width="80%"><h1>Eden admin page: <?php echo $_SERVER['REMOTE_USER']; ?></h1></td>
        <td width="20%" align="center"><a href="https://eden.imtbs-tsp.eu">Home</a></td>
    </tr>
</table>

<p><b>A implementer</b></p>

<ul>
    <li>Toutes les fonctions des pages students & teachers pour tous les utilisateurs</li>
</ul>

<pre>
<?php
    echo ($_SERVER['REMOTE_USER'] ?? '').PHP_EOL;
    print_r($_SERVER);
?>
</pre>

</body>
</html>
