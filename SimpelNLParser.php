<?php

class SimpelNLParser
{
  private $url = 'https://www.mijnsimpel.nl';
  private $useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36';
  private $cookiejar;

  public function __construct()
  {
    $this->cookiejar = tempnam(sys_get_temp_dir(), 'SimpelNLParser');
  }

  public function __destruct()
  {
    unlink($this->cookiejar);
  }

  private function doRequest($method, $url, $data = null)
  {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: ' . $this->useragent));
    curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiejar);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiejar);

    curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');

    if ($data != null && $method != 'GET')
    {
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    elseif ($data != null) {
      curl_setopt($curl, CURLOPT_URL, $this->url . $url . '?' . $this->http_build_query($data));
    }

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($httpcode != 200)
      $response = null;

    curl_close($curl);

    return $response;
  }

  private function http_build_query($array)
  {
    $params = array();
    foreach ($array as $key => $value)
    {
      if (!is_array($value))
        $params[] = $key . "=" . (is_bool($value) ? ($value ? 'true' : 'false') : urlencode($value));
    }
    return implode("&", $params);
  }

  public function doLogin($username, $password)
  {
    $body = '__EVENTTARGET=ctl00%24content%24lbSubmit&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUKMTY0MDkzNDg2Ng9kFgJmD2QWBAIBD2QWAgIJD2QWCGYPFgIeBFRleHQFATdkAgEPFgIfAAUEdHJ1ZWQCAg8WAh8ABQYmbmJzcDtkAgMPFgIfAAVzPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMThweDsgZm9udC13ZWlnaHQ6IG5vcm1hbDsiPkRlIGluZ2V2dWxkZSBnZWJydWlrZXJzbmFhbSBlbiBvZiB3YWNodHdvb3JkIGlzIG9uanVpc3QuPC9zcGFuPmQCAw9kFggCAQ8WAh8ABcoCPGRpdiBjbGFzcz0ibWVudSI%2BPHNwYW4gaWQ9Im1lbnVfdGFyaWV2ZW4iPjxhIGhyZWY9Imh0dHBzOi8vd3d3LnNpbXBlbC5ubC90YXJpZXZlbiIgdGFyZ2V0PSJfYmxhbmsiPnRhcmlldmVuPC9hPjwvc3Bhbj48c3BhbiBpZD0ibWVudV9mYXEiPjxhIGhyZWY9Imh0dHBzOi8vd3d3LnNpbXBlbC5ubC9rbGFudGVuc2VydmljZSIgdGFyZ2V0PSJfYmxhbmsiPnZyYWdlbjwvYT48L3NwYW4%2BPHNwYW4gaWQ9Im1lbnVfZmFxIj48YSBocmVmPSJodHRwOi8vdmVybGVuZ2VuLnNpbXBlbC5ubC8iIHRhcmdldD0iX2JsYW5rIj5hYm9ubmVtZW50IHZlcmxlbmdlbjwvYT48L3NwYW4%2BPC9kaXY%2BZAIFDxYCHwBlZAIHD2QWBAIHDxYCHwAF%2FAE8dGQgY2xhc3M9InRkNCI%2BPGltZyBzdHlsZT0iZmxvYXQ6IGxlZnQ7IiBzcmM9Ii9yZXNvdXJjZXMvaW1nL21pc2MvdG9vbHRpcC5wbmciIGNsYXNzPSJ0b29sdGlwdGFyZ2V0bGluayIgdGl0bGU9IkJlbiBqZSBqZSBnZWJydWlrZXJzbmFhbSB2ZXJnZXRlbj8gPGEgaHJlZj1odHRwczovL3d3dy5taWpuc2ltcGVsLm5sL3JlcXVlc3RfdXNlcm5hbWUuYXNweD5LbGlrIGRhbiBoaWVyPC9hPiBvbSBkZXplIG9wIHRlIHZyYWdlbi4iIC8%2BPC90ZD5kAgsPFgIfAAXJAjx0ZCBjbGFzcz0idGQ0Ij48aW1nIHN0eWxlPSJmbG9hdDogbGVmdDsiIHNyYz0iL3Jlc291cmNlcy9pbWcvbWlzYy90b29sdGlwLnBuZyIgY2xhc3M9InRvb2x0aXB0YXJnZXRsaW5rIiB0aXRsZT0iTGV0IG9wOiBKZSBrdW50IGhpZXIgbmlldCBpbmxvZ2dlbiBtZXQgZGUgY29kZSBkaWUgamUgcGVyIGUtbWFpbCB2YW4gb25zIGhlYnQgb250dmFuZ2VuLiBHYSBuYWFyIDxhIGhyZWY9aHR0cHM6Ly93d3cubWlqbnNpbXBlbC5ubC93YWNodHdvb3JkLmFzcHg%2Bd3d3Lm1pam5zaW1wZWwubmwvd2FjaHR3b29yZDwvYT4gZW4gbWFhayBlZW4gd2FjaHR3b29yZCBhYW4uIiAvPjwvdGQ%2BZAILDw8WBB8ABQl1aXRsb2dnZW4eB1Zpc2libGVoZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgEFGWN0bDAwJGNvbnRlbnQkY2hrUmVtZW1iZXKM5jHW54H8DmjNZzL1jSgLB3j%2B8Q%3D%3D&__VIEWSTATEGENERATOR=C2EE9ABB&__EVENTVALIDATION=%2FwEWBgKqjcnuDgKclevVBgLaptSZDAKekIsoAo%2BD3Z0LArOJ2fsB0YS1C%2F5GYR5VqDvO3Bt8f%2FvaZaY%3D&ctl00%24content%24txtLoginName=' . $username . '&ctl00%24content%24txtPassword=Delano2802&ctl00%24content%24hdnLoginWW=' . $password;

    $this->doRequest('POST', $this->url . '/login.aspx', $body);
  }

  public function getUsage()
  {
    $body = $this->doRequest('GET', $this->url . '/credit.aspx');
    $response = array();

    if (preg_match_all('/<div class="bar_load" id="(.*?)">.*?<div class="progress-label"><span>([0-9]+).*?<span style="float: right">([0-9]+) (.*?)<\/span>/is', $body, $matches))
    {
      for ($i = 0; $i < count($matches[0]); $i ++)
      {
        $response[$matches[1][$i]] = array(
          'current' => (int)$matches[2][$i],
          'max' => (int)$matches[3][$i],
          'type' => $matches[4][$i]
        );
      }
    }

    return $response;
  }

  public function doLogout()
  {
    $body = '__EVENTTARGET=ctl00%24lbLogOut&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTE2NDQxNDM0MDIPZBYCZg9kFgICAw9kFgYCAQ8WAh4EVGV4dAXKAjxkaXYgY2xhc3M9Im1lbnUiPjxzcGFuIGlkPSJtZW51X3RhcmlldmVuIj48YSBocmVmPSJodHRwczovL3d3dy5zaW1wZWwubmwvdGFyaWV2ZW4iIHRhcmdldD0iX2JsYW5rIj50YXJpZXZlbjwvYT48L3NwYW4%2BPHNwYW4gaWQ9Im1lbnVfZmFxIj48YSBocmVmPSJodHRwczovL3d3dy5zaW1wZWwubmwva2xhbnRlbnNlcnZpY2UiIHRhcmdldD0iX2JsYW5rIj52cmFnZW48L2E%2BPC9zcGFuPjxzcGFuIGlkPSJtZW51X2ZhcSI%2BPGEgaHJlZj0iaHR0cDovL3Zlcmxlbmdlbi5zaW1wZWwubmwvIiB0YXJnZXQ9Il9ibGFuayI%2BYWJvbm5lbWVudCB2ZXJsZW5nZW48L2E%2BPC9zcGFuPjwvZGl2PmQCBQ8WAh8ABb8GPHVsIGNsYXNzPSJtZW51Ij48bGk%2BPGEgaWQ9ImhvbWUiIGhyZWY9Ii9sYW5kaW5nLmFzcHgiPmhvbWU8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImFjY291bnQiIGhyZWY9Ii9hY2NvdW50LmFzcHgiPm1pam4gZ2VnZXZlbnM8L2E%2BPC9saT48bGk%2BPGEgaWQ9InNpbXByb3BlcnRpZXMiIGhyZWY9Ii9zaW1wcm9wZXJ0aWVzLmFzcHgiPmdlZ2V2ZW5zIGFib25uZW1lbnQ8L2E%2BPC9saT48bGk%2BPGEgaWQ9InVwZ3JhZGUiIGhyZWY9Ii91cGdyYWRlLmFzcHgiPndpanppZyBhYm9ubmVtZW50PC9hPjwvbGk%2BPGxpPjxhIGlkPSJiZWxwbGFmb25kIiBocmVmPSIvYmVscGxhZm9uZC5hc3B4Ij5wbGFmb25kPC9hPjwvbGk%2BPGxpPjxhIGlkPSJpbnZvaWNlcyIgaHJlZj0iL2ludm9pY2VzLmFzcHgiPmZhY3R1cmVuPC9hPjwvbGk%2BPGxpPjxhIGlkPSJjYWxsX2RldGFpbHMiIGhyZWY9Ii9yZXF1ZXN0X3NwZWNpZmljYXRpb25zLmFzcHgiPmJlbHNwZWNpZmljYXRpZTwvYT48L2xpPjxsaT48YSBpZD0iY3JlZGl0IiBocmVmPSIvY3JlZGl0LmFzcHgiPmFjdHVlZWwgdmVyYnJ1aWs8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImNoYW5nZV9wYXNzd29yZCIgaHJlZj0iL3Bhc3N3b3JkX2VkaXQuYXNweCI%2Bd2lqemlnIHdhY2h0d29vcmQ8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImJsb2NrIiBocmVmPSIvYmxvY2suYXNweCI%2BYmxva2tlZXIgbnVtbWVyPC9hPjwvbGk%2BPGxpPjxhIGlkPSJzaW1zd2FwIiBocmVmPSIvU2ltU3dhcC5hc3B4Ij52ZXJ2YW5nZW5kZSBTaW1rYWFydDwvYT48L2xpPjxsaT48YSBpZD0iY29udGFjdCIgaHJlZj0iL2NvbnRhY3QuYXNweCI%2Bc3RlbCBqZSB2cmFhZzwvYT48L2xpPjwvdWw%2BZAIHD2QWCgIFD2QWAmYPFgIfAGVkAgcPZBYCZg8WAh8AZWQCCQ8WAh8ABQ1ELiBSZWlqbmllcnNlZAINDxYCHgdWaXNpYmxlaBYEAgIPFgIfAWhkAgMPFgIfAWgWAgIFDxYCHwBlZAIPDxYCHwFoFgICAg8WAh8BaGRkoXYGy9NJgk1Xue2NCGC7hKy1tqQ%3D&__VIEWSTATEGENERATOR=318D363F&__EVENTVALIDATION=%2FwEWAgKJxqeYDQLHjbmPC1dJWUy%2Fv4bcBVwdojseXRI6oLrd';

    $this->doRequest('POST', $this->url . '/logout.aspx', $body);
  }
}
