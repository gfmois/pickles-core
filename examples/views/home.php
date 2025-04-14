<html>
    <h1>Hello to Pickles View Engine</h1>
    <h1>Showing content for <?= $user ?></h1>
    <?php foreach (["Nice", "Wow"] as $message) { ?>
        <p><?= $message ?></p>
    <?php } ?>
</html>