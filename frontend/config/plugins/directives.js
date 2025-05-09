// plugins/directives.js
const clickOutsideDirective = {
  beforeMount(el, binding) {
    el.__clickOutsideHandler__ = (event) => {
      // Check if element is visible in DOM
      const isVisible = el.offsetParent !== null;
      console.log('v', el, el.offsetParent, isVisible)
      console.log('isoutside', el === event.target || el.contains(event.target))
      if (isVisible && !(el === event.target || el.contains(event.target))) {
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