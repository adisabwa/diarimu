// plugins/directives.js
const clickOutsideDirective = {
  beforeMount(el, binding) {
    el.__clickOutsideHandler__ = (event) => {
      // Check if element is visible in DOM
      const isVisible = el.offsetParent !== null;
      // Only trigger if the element is visible AND the click was outside
      const clickedOutside = !el.contains(event.target);

      console.log(isVisible, clickedOutside)
      if (isVisible && clickedOutside) {
        // console.log(binding.value)
        binding.value(event);
      }
    };

    document.addEventListener('click', el.__clickOutsideHandler__);
  },
  unmounted(el) {
    document.removeEventListener('click', el.__clickOutsideHandler__);
    delete el.__clickOutsideHandler__;
  }
};

const clickExcludeIdDirective = {
  beforeMount(el, binding) {
    const { handler, excludeIds = [] } = binding.value

    el.__clickOutsideHandler__ = (event) => {
      const isClickInside = el.contains(event.target)
      const isClickOnExcludedId = excludeIds.some(id =>
        event.target.closest(`#${id}`)
      )

      if (!isClickInside && !isClickOnExcludedId) {
        handler(event)
      }
    }

    document.addEventListener('click', el.__clickOutsideHandler__)
  },
  unmounted(el) {
    document.removeEventListener('click', el.__clickOutsideHandler__)
    delete el.__clickOutsideHandler__
  }
};

  export default {
    install(app) {
      app.directive('click-outside', clickOutsideDirective);
      app.directive('click-exclude-id', clickExcludeIdDirective);
      // Add more directives here if needed
    }
  };