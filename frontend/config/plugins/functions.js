function scrollTo(to, duration, onDone) {
  var start = window.scrollY,
      change = to - start,
      startTime = performance.now(),
      val, now, elapsed, t;
  // console.log(to, start, change)
  function animateScroll(){
      now = performance.now();
      elapsed = (now - startTime)/1000;
      t = (elapsed/duration);

      window.scrollTo(0, start + change * easeInOutQuad(t));

      if( t < 1 )
          window.requestAnimationFrame(animateScroll);
      else
          onDone && onDone();
  };

  animateScroll();
} 

function scrollToCoordinate(parent, coordinate, duration, scroll = 'left', rerun = false, loop = true, onDone){
  let to = coordinate; 
  var start = scroll == 'left' ? parent.scrollLeft : parent.scrollTop,
    change = to - start,
    startTime = performance.now(),
    padding = 0,
    val, now, elapsed, t;

  // console.log(coordinate, parent.clientWidth, parent.scrollWidth)
  if ( (coordinate + parent.clientWidth) > parent.scrollWidth ) {
    // console.log('more than max');
    coordinate = parent.scrollWidth - parent.clientWidth
    // return;
  }

  let scrolling = false;
  let scrollTimer = -1;
  parent.addEventListener("mousewheel", (event) => {
    // console.log('scroll')
    scrolling = true;
    if (scrollTimer != -1)
      clearTimeout(scrollTimer);

    scrollTimer = window.setTimeout(function(){
      // console.log('scroll-end')
      scrolling = false;
      window.setTimeout(function(){
        if (rerun) {
          // console.log('re-run animation')
          recursive();
        }
      }, 500);
    }, 500);

  });

  function recursive(){
    scrollToCoordinate(parent, coordinate, duration, scroll, rerun, onDone)
  }

  let reqAnim = 0;
  let strTime = startTime
  
  function animateScroll(){
    now = performance.now();
    elapsed = (now - startTime)/1000;
    t = (elapsed/duration);

    let plus = change * easeInOutQuad(t) - padding
    // console.log(change, easeInOutQuad(t), padding )
    if (scroll == 'left') {
      parent.scrollLeft = start + plus;
      if (Math.abs(parent.scrollWidth - parent.clientWidth - parent.scrollLeft) < 1) {
        // t = 0;
        startTime = performance.now()
        parent.scrollLeft = to;
        t = 1;
      }
    }
    else {
      parent.scrollTop = start + plus;
      if (Math.abs(parent.scrollHeight - parent.clientHeight - parent.scrollTop) < 1) {
        // t = 0;
        startTime = performance.now()
        parent.scrollTop = to;
        t = 1;
        // console.log(parent.scrollTop, 'scrolled-bottom')
      }
    }

    let continueAnimating = true;
    if (scrolling)
      continueAnimating = false;


    // console.log(parent.scrollTop)
    if( t < 1 && continueAnimating) {
        reqAnim = window.requestAnimationFrame(animateScroll);
    } else {
        onDone && onDone();
    }
  };

  reqAnim = window.requestAnimationFrame(animateScroll);
};

function getRelativePos(elm){
var pPos = elm.parentNode.getBoundingClientRect(), // parent pos
    cPos = elm.getBoundingClientRect(), // target pos
    pos = {};

// console.log(cPos, pPos)
pos.top    = cPos.top    - pPos.top,
pos.right  = cPos.right  - pPos.right,
pos.bottom = cPos.bottom - pPos.bottom,
pos.left   = cPos.left   - pPos.left;

return pos;
}
  
function easeInOutQuad(t){ 
return t <.5 ? 2*t*t : -1+(4-2*t)*t 
};

MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

const observer = new MutationObserver(function(mutationList, observer) {
  for (const mutation of mutationList) {
    if (mutation.type === "childList") {
      console.log("A child node has been added or removed.");
    } else if (mutation.type === "attributes") {
      console.log(`The ${mutation.attributeName} attribute was modified.`);
    }
  }
});

import moment from 'moment/src/moment';
import id from 'moment/src/locale/id';

moment.updateLocale('id', id);

let listFunction = {
  toIDR: function(number) {
    if (number == null || number == undefined) return 'Rp. 0,00';
    number = Math.round(number);
    return "Rp " + number.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.") + ",00";
  },
  setCurrency: function(number) {
    number = listFunction.toNumber(number)
    return number.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.");
  },
  toNumber: function(number){
    if (number == null || number == undefined) return 0;
    console.log(number, typeof number)
    if (typeof number == 'string') 
      number = number.replace(/[^0-9]+/g, '');

    if (number == '') return 0
    console.log(number)
    number = parseInt(number)
    console.log(number)
    number = Math.round(number)

    return number
  },
  isEmpty(str) {
    return (!str || 0 === str.length || str === undefined || str === '0000-00-00');
  },
  ucFirst: str => str ? str[0].toUpperCase() + str.slice(1) : str,
  jquery(val){
    return jquery(val);
  },
  observeMutation(target, config = { attributes: true }){
    let targetNode = jquery(target)[0];
    observer.observe(targetNode, config);
  },
  getRelativePos(el){
    // console.log(el)
    el = jquery(el)[0];
    // console.log(el)
    if (el) {
      return getRelativePos(el)
    } 
    return null;
  },
  scrollToElement(el, duration = 2){
    el = jquery(el)[0];
    if (el) {
      var pos = getRelativePos(el);
      // console.log(pos)
      scrollTo(pos.top , duration); 
      // console.log(getRelativePos(el))
      // el.scrollIntoView({ behavior: "smooth" });
    }
  },
  scrollElement(parent, destination, duration = 2, scroll = 'left', rerun = true){
    parent = jquery(parent)[0];
    let el = jquery(parent).find(destination)[0];
    // console.log(parent, destination, el)
    if (el) {
      var pos = getRelativePos(el);
      let coordinate = scroll == 'left' ? pos.left : pos.bottom;
      // console.log(parent.scrollLeft, coordinate)
      scrollToCoordinate(parent, coordinate, duration, scroll, rerun)
    }
  },
  range(size, startAt = 0) {
    return [...Array(size).keys()].map(i => i + startAt);
  },
  scrollToCoordinate(element, coordinate, duration, scroll = 'left', rerun = true){
    element = jquery(element)[0];
    scrollToCoordinate(element, coordinate, duration, scroll, rerun)
  },
  toggleClass(el, cls, delay = 0){
    // console.log(el)
    window.setTimeout(function () {
      return jquery(el).toggleClass(cls);
    }, delay);
  },
  addClass(el, cls, delay = 0){
    // console.log('add')
    setTimeout(function(){
      return jquery(el).addClass(cls);
    }, delay);
  },
  removeClass(el, cls, delay = 0){
    setTimeout(function(){
      // console.log('remove')
      return jquery(el).removeClass(cls);
    }, delay);
  },
  checkScroll(position) {
    var currentScrollPosition = window.scrollY
    let up = true;
    if(currentScrollPosition < position){
      up = true;
      //your desire logic 
    }
    else{
      up = false;
      //your desire logic 
    }

    return {up, currentScrollPosition};
  },
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
  dateNow() {
    return moment().format('yyyy-MM-DD');
  },
  dateIndo(date) {
    return moment(date).format('DD MMMM yyyy');
  },
  dateDayIndoSeparate(date) {
    let data = moment(date).format('dddd DD MMM MM MMMM yyyy HH:mm');
    let keys = ['long-day','day','short-month','month','long-month','year','time'];
    data = data.split(' ');
    let result = {};
    for (var i = 0; i < data.length; i++) {
      data[i] = data[i].trim()
      result[keys[i]] = data[i];
    }
    return result
  },
  dateMonthIndo(date) {
    return moment(date).format('DD MMMM');
  },
  dateDayIndo(date) {
    return moment(date).format('dddd, DD MMMM YYYY');
  },
  dateShortIndo(date) {
    return moment(date).format('DD/MM/YY');
  },
  dateTimeIndo(datetime) {
    return moment(datetime).format('dddd, DD MMMM YYYY HH:mm');
  },
  timeIndo(datetime) {
    return moment(datetime).format('HH:mm');
  },
  monthIndo(date) {
    return moment(date).format('MMMM YYYY');
  },
  addDay(date, sum, unit = 'days'){
    return moment(date).add(sum, unit).format('yyyy-MM-DD');
  },
  getWeeklyRanges(lastDateStr, numberOfWeeks) {
    const result = [];
    const lastDate = new Date(lastDateStr);

    for (let i = 0; i < numberOfWeeks; i++) {
        // Clone the date to avoid modifying lastDate
        const endOfWeek = new Date(lastDate);
        endOfWeek.setDate(lastDate.getDate() - (7 * i));

        const startOfWeek = new Date(endOfWeek);
        startOfWeek.setDate(endOfWeek.getDate() - 6);

        result.push({
            start: startOfWeek.toISOString().slice(0, 10),
            end: endOfWeek.toISOString().slice(0, 10)
        });
    }

    return result;
  },
  getMonthlyRanges(lastDateStr, numberOfMonths) {
    const result = [];
    let current = new Date(lastDateStr);

    // Normalize to first of the month
    current.setDate(1);

    for (let i = 0; i < numberOfMonths; i++) {
        const startOfMonth = new Date(current);
        
        // Get last day of current month
        const endOfMonth = new Date(current.getFullYear(), current.getMonth() + 1, 0);

        result.push({
            start: startOfMonth.toISOString().slice(0, 10),
            end: endOfMonth.toISOString().slice(0, 10)
        });

        // Move to first day of next month
        current.setMonth(current.getMonth() - 1);
    }

    return result;
  },
  ucFirst (str) {
    return str ? str[0].toUpperCase() + str.slice(1) : str
  },
  openLink(link){
    window.open(link,'_blank');
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
  copyText(link) {
    const textArea = document.createElement("textarea");
    textArea.value = link
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      document.execCommand('copy');
      alert('Link berhasil di copy: ' + link)
    } catch (err) {
      alert('Unable to copy to clipboard', err);
    }
    document.body.removeChild(textArea);
  },
  runFunction(_func, data, options = []) {
    if (listFunction.isEmpty(_func)) {
      if (listFunction.isEmpty(options))
        return data
      else {
        for (let index = 0; index < options.length; index++) {
          const el = options[index];
          if (el.value == data)
            return el.label
        }
      }
    } else { 
      return ( typeof _func == 'string' ? listFunction[_func](data) : _func(data) )
    }
    return data
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