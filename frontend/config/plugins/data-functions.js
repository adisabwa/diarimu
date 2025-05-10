
let listFunction = {
    fillObjectValue(src, data) {
      Object.keys(src).forEach(key => {
        if (data.hasOwnProperty(key)) {
          src[key] = data[key];
        }
      });
      return src;
    },
    fillAndAddObjectValue(src, data) {
      let adds = []
      Object.keys(data).forEach(key => {
        if (src.hasOwnProperty(key)) {
          src[key] = data[key];
        } else {
          adds[key] = data[key]
        }
      });
      return {...src,...adds};
    },
    resetObjectValue(src, exception = []) {
      Object.keys(src).forEach(key => {
        if (!exception.includes(key))
          src[key] = null; 
      });
      return src;
    },
    traverse(obj, callback, path = '') {
      Object.entries(obj).forEach(([key, value]) => {
        const fullPath = path ? `${path}.${key}` : key;
    
        if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
          listFunction.traverse(value, callback, fullPath); // recurse
        } else {
          callback(fullPath, value); // base case
        }
      });
    },
    getObjectValueByPath(obj, path) {
      return path.split('.').reduce((acc, key) => acc?.[key], obj);
    },
    setObjectValueByPath(obj, path, value) {
      const keys = path.split('.'); // Split the path into individual keys
      let current = obj;
    
      // Iterate over the keys to find the target location
      keys.forEach((key, index) => {
        if (index === keys.length - 1) {
          // If we're at the last key, set the value
          current[key] = value;
        } else {
          // If the key doesn't exist, create it (if it's not an array)
          if (current[key] === undefined) {
            current[key] = isNaN(keys[index + 1]) ? {} : [];
          }
          current = current[key];
        }
      });
    },
    coalesce(array) {
      let check = false;
      let value = null;
      array.forEach(a => {
        // console.log(a);
        if (!check && a !== null && a !== undefined) {
          value = a;
          check = true;
        }
      });
      return value;
    },
    setCookie(name, value, days) {
      let expires = "";
      if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
      }
      value = JSON.stringify(value)
      console.log(value)
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
    },
    getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      console.log(ca)
      let value = null
      for (let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) === ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) === 0) {
            value = c.substring(nameEQ.length, c.length);
            if (value != '')
              value = JSON.parse(value)
          }
      }
      return value;;
    },
    deleteCookie(name) {
      document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
  }
  
  export { listFunction };
  
  export default {
    install: (app) => {
      let keys = Object.keys(listFunction)
      for (var i = 0; i < keys.length; i++) {
        let ind = keys[i]
        app.config.globalProperties[ind] = listFunction[ind]
      }
    }
  }