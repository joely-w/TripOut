<!-- Footer -->
<?
if (isset($scripts_footer)) { #If any unique scripts are required, set in pages $script array.
    foreach ($scripts_footer as $path) {
        ?>
        <script src="<?php echo $path ?>"></script>
        <?
    }
}
?>
<footer id="sticky-footer" class="py-4 bg-dark text-white-50">
    <div class="container text-center">
        <small>Copyright &copy; TripOut 2019</small>
    </div>
</footer>
<!-- Footer -->