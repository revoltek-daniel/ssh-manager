import { Controller } from 'stimulus'

export default class extends Controller {
  static targets = [ "link" ]

  connect () {
    let parentElement = document.getElementById('serverlist')
    this.urls = {
      'remove': parentElement.dataset.removeUrl,
      'assign': parentElement.dataset.assignUrl
    }
  }

  handleClick () {
    let element = this.linkTarget
    console.log(element)
    const action = element.dataset.type
    console.log(action)
    const serverId = element.dataset.serverid

    let url = this.urls[action]
    url = url.replace('-placeholder', serverId)

    let xhttp = new XMLHttpRequest()
    xhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        let result = JSON.parse(this.responseText)

        if (result.success) {
          element.innerText = result.text
          element.dataset.type = action === 'assign' ? 'remove' : 'assign'
        }
      }
    }
    xhttp.open('GET', url, true)
    xhttp.send()
  }
}
