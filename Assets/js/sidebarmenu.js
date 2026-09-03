

$(function () {
    "use strict";
    var url = window.location + "";
    var path = url.replace(
      window.location.protocol + "//" + window.location.host + "/",
      ""
    );
    var element = $("ul#sidebarnav a").filter(function () {
      return this.href === url || this.href === path; // || url.href.indexOf(this.href) === 0;
    });
    element.parentsUntil(".sidebar-nav").each(function (index) {
      if ($(this).is("li") && $(this).children("a").length !== 0) {
        $(this).children("a").addClass("active");
        $(this).parent("ul#sidebarnav").length === 0
          ? $(this).addClass("active")
          : $(this).addClass("selected");
      } else if (!$(this).is("ul") && $(this).children("a").length === 0) {
        $(this).addClass("selected");
      } else if ($(this).is("ul")) {
        $(this).addClass("in");
      }
    });
  
    element.addClass("active");
    $("#sidebarnav a").on("click", function (e) {

      let $this = $(this);
      let $parentUl = $this.parents("ul:first");
      let $submenu = $this.next("ul");

      if ($this.hasClass("has-arrow")) {
          e.preventDefault();
      }

      let isOpen = $this.hasClass("active");

      // Close all first
      $parentUl.find("ul.in").removeClass("in");
      $parentUl.find("ul.show").removeClass("show");
      $parentUl.find("a.active").removeClass("active");

      if (!isOpen) {
          // Open only if it was NOT already open
          if ($submenu.length) {
              $submenu.addClass("in");
          }
          $this.addClass("active");
      }

  });
    

    // $("#btn_close_bill_counter").click(function(){
    //   $("#close-bill-counter").modal('hide');
    // });//close modal
  });
  