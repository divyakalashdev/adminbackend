<!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Arkh Digital 2022 - Developed by Brandsteed</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->
<script type="text/javascript">
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    function logoutMe(){
        document.cookie = 'mid=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        location.reload();
    }
</script>

</body>

</html>
