import { Controller } from 'stimulus';

export default class extends Controller {
  connect() {
    addEventListenerByClass('js-click', 'click', function (event) {
      event.preventDefault()
      console.log(event.target.href)
    })

    function addEventListenerByClass(className, event, fn) {
      let list = document.getElementsByClassName(className);
      for (let i = 0, len = list.length; i < len; i++) {
        list[i].addEventListener(event, fn, false);
      }
    }

  }
}
