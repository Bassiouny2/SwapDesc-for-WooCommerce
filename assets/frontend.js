jQuery(function ($) {
  const $roots = $(".pvt-description-root");
  if (!$roots.length) return;

  function hasVisibleContent(html) {
    if (!html) return false;
    const $temp = $("<div>").html(html);
    const text = $.trim($temp.text());
    if (text.length) return true;
    return $temp.find("img,video,iframe,svg,table,ul,ol,blockquote").length > 0;
  }

  function applyBackground($el, visible) {
    if (!visible) {
      $el.css("background-color", "");
      return;
    }
    const bg = $el.data("pvtBg");
    $el.css("background-color", bg ? bg : "");
  }

  $roots.each(function () {
    const $el = $(this);
    const initial = $el.html();
    $el.data("pvtInitial", initial);
    const visible = hasVisibleContent(initial);
    if (!visible) {
      $el.hide();
    }
    applyBackground($el, visible);
  });

  const fadeDuration = 200;
  const cache = {};

  function swapDescription(html) {
    const visible = hasVisibleContent(html);
    $roots.each(function () {
      const $el = $(this);
      if (!visible) {
        $el.stop(true, true).fadeOut(fadeDuration, function () {
          $el.html("");
          applyBackground($el, false);
        });
        return;
      }
      $el.stop(true, true).fadeOut(fadeDuration, function () {
        $el.html(html);
        applyBackground($el, true);
        $el.fadeIn(fadeDuration);
      });
    });
  }

  function getDescFromPayload(variation) {
    return variation && variation.pvt_description
      ? variation.pvt_description
      : "";
  }

  function fetchDescriptionAjax(variationId) {
    if (cache[variationId]) {
      swapDescription(cache[variationId]);
      return;
    }
    $.post(PVT_DATA.ajax_url, {
      action: "pvt_get_variation_description",
      variation_id: variationId,
    }).done(function (res) {
      if (res && res.success && res.data && res.data.description) {
        cache[variationId] = res.data.description;
        swapDescription(res.data.description);
      }
    });
  }

  // Hook into WooCommerce variation selection
  $(document.body).on("found_variation", function (e, variation) {
    const html = getDescFromPayload(variation);
    if (html) {
      swapDescription(html);
    } else if (variation && variation.variation_id) {
      fetchDescriptionAjax(variation.variation_id);
    } else {
      const initial = $roots.first().data("pvtInitial");
      if (typeof initial !== "undefined") {
        swapDescription(initial);
      }
    }
  });

  // Restore last selected variation from session if available
  const lastVar = sessionStorage.getItem("pvt_last_variation_id");
  if (lastVar) {
    fetchDescriptionAjax(parseInt(lastVar, 10));
  }
  $(document.body).on("found_variation", function (e, variation) {
    if (variation && variation.variation_id) {
      sessionStorage.setItem("pvt_last_variation_id", variation.variation_id);
    }
  });

  $("form.variations_form").on("reset_data", function () {
    const initial = $roots.first().data("pvtInitial");
    if (typeof initial !== "undefined") {
      swapDescription(initial);
    }
  });
});
