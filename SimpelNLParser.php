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

    //curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');

    if ($data != null && $method != 'GET')
    {
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
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
    $body = '__EVENTTARGET=ctl00%24content%24lbSubmit&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTE5MDIyMTM1OTQPZBYCZg9kFgICAw9kFggCAQ8WAh4EVGV4dAXeATxkaXYgY2xhc3M9Im1lbnUiPjxzcGFuIGlkPSJtZW51X3RhcmlldmVuIj48YSBocmVmPSJodHRwOi8vd3d3LnNpbXBlbC5ubC9UYXJpZXZlbi5hc3B4IiB0YXJnZXQ9Il9ibGFuayI%2BdGFyaWV2ZW48L2E%2BPC9zcGFuPjxzcGFuIGlkPSJtZW51X2ZhcSI%2BPGEgaHJlZj0iaHR0cDovL3d3dy5zaW1wZWwubmwvZmFxLmFzcHgiIHRhcmdldD0iX2JsYW5rIj52cmFnZW48L2E%2BPC9zcGFuPjwvZGl2PmQCBQ8WAh8AZWQCBw9kFgQCCQ8WAh8ABecBPHRkIGNsYXNzPSJ0ZDQiPjxpbWcgc3JjPSIvcmVzb3VyY2VzL2ltZy9taXNjL3Rvb2x0aXAucG5nIiBjbGFzcz0idG9vbHRpcHRhcmdldGxpbmsiIHRpdGxlPSJCZW4gamUgamUgZ2VicnVpa2Vyc25hYW0gdmVyZ2V0ZW4%2FIDxhIGhyZWY9aHR0cHM6Ly93d3cubWlqbnNpbXBlbC5ubC9yZXF1ZXN0X3VzZXJuYW1lLmFzcHg%2BS2xpayBkYW4gaGllcjwvYT4gb20gZGV6ZSBvcCB0ZSB2cmFnZW4uIiAvPjwvdGQ%2BZAIPDxYCHwAFtAI8dGQgY2xhc3M9InRkNCI%2BPGltZyBzcmM9Ii9yZXNvdXJjZXMvaW1nL21pc2MvdG9vbHRpcC5wbmciIGNsYXNzPSJ0b29sdGlwdGFyZ2V0bGluayIgdGl0bGU9IkxldCBvcDogSmUga3VudCBoaWVyIG5pZXQgaW5sb2dnZW4gbWV0IGRlIGNvZGUgZGllIGplIHBlciBlLW1haWwgdmFuIG9ucyBoZWJ0IG9udHZhbmdlbi4gR2EgbmFhciA8YSBocmVmPWh0dHBzOi8vd3d3Lm1pam5zaW1wZWwubmwvd2FjaHR3b29yZC5hc3B4Pnd3dy5taWpuc2ltcGVsLm5sL3dhY2h0d29vcmQ8L2E%2BIGVuIG1hYWsgZWVuIHdhY2h0d29vcmQgYWFuLiIgLz48L3RkPmQCCw8PFgIeB1Zpc2libGVoZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgEFGWN0bDAwJGNvbnRlbnQkY2hrUmVtZW1iZXIiAtwewvJHfbs3afCcHK6ltF2ULg%3D%3D&__VIEWSTATEGENERATOR=C2EE9ABB&__EVENTVALIDATION=%2FwEWBgK%2F%2FL6QCAKclevVBgLaptSZDAKekIsoAo%2BD3Z0LArOJ2fsBv%2B%2BE9%2BO%2FtQVgPxcFm4ZRqT9uu%2Fc%3D&ctl00%24content%24txtLoginName=' . $username . '&ctl00%24content%24txtPassword=' . $password . '&ctl00%24content%24hdnLoginWW=' . $password;

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
    $body = '__EVENTTARGET=ctl00%24lbLogOut&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTE4MDgzOTU5MjcPZBYCZg9kFgICAw9kFgYCAQ8WAh4EVGV4dAXeATxkaXYgY2xhc3M9Im1lbnUiPjxzcGFuIGlkPSJtZW51X3RhcmlldmVuIj48YSBocmVmPSJodHRwOi8vd3d3LnNpbXBlbC5ubC9UYXJpZXZlbi5hc3B4IiB0YXJnZXQ9Il9ibGFuayI%2BdGFyaWV2ZW48L2E%2BPC9zcGFuPjxzcGFuIGlkPSJtZW51X2ZhcSI%2BPGEgaHJlZj0iaHR0cDovL3d3dy5zaW1wZWwubmwvZmFxLmFzcHgiIHRhcmdldD0iX2JsYW5rIj52cmFnZW48L2E%2BPC9zcGFuPjwvZGl2PmQCBQ8WAh8ABb8GPHVsIGNsYXNzPSJtZW51Ij48bGk%2BPGEgaWQ9ImhvbWUiIGhyZWY9Ii9sYW5kaW5nLmFzcHgiPmhvbWU8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImFjY291bnQiIGhyZWY9Ii9hY2NvdW50LmFzcHgiPm1pam4gZ2VnZXZlbnM8L2E%2BPC9saT48bGk%2BPGEgaWQ9InNpbXByb3BlcnRpZXMiIGhyZWY9Ii9zaW1wcm9wZXJ0aWVzLmFzcHgiPmdlZ2V2ZW5zIGFib25uZW1lbnQ8L2E%2BPC9saT48bGk%2BPGEgaWQ9InVwZ3JhZGUiIGhyZWY9Ii91cGdyYWRlLmFzcHgiPndpanppZyBhYm9ubmVtZW50PC9hPjwvbGk%2BPGxpPjxhIGlkPSJiZWxwbGFmb25kIiBocmVmPSIvYmVscGxhZm9uZC5hc3B4Ij5wbGFmb25kPC9hPjwvbGk%2BPGxpPjxhIGlkPSJpbnZvaWNlcyIgaHJlZj0iL2ludm9pY2VzLmFzcHgiPmZhY3R1cmVuPC9hPjwvbGk%2BPGxpPjxhIGlkPSJjYWxsX2RldGFpbHMiIGhyZWY9Ii9yZXF1ZXN0X3NwZWNpZmljYXRpb25zLmFzcHgiPmJlbHNwZWNpZmljYXRpZTwvYT48L2xpPjxsaT48YSBpZD0iY3JlZGl0IiBocmVmPSIvY3JlZGl0LmFzcHgiPmFjdHVlZWwgdmVyYnJ1aWs8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImNoYW5nZV9wYXNzd29yZCIgaHJlZj0iL3Bhc3N3b3JkX2VkaXQuYXNweCI%2Bd2lqemlnIHdhY2h0d29vcmQ8L2E%2BPC9saT48bGk%2BPGEgaWQ9ImJsb2NrIiBocmVmPSIvYmxvY2suYXNweCI%2BYmxva2tlZXIgbnVtbWVyPC9hPjwvbGk%2BPGxpPjxhIGlkPSJzaW1zd2FwIiBocmVmPSIvU2ltU3dhcC5hc3B4Ij52ZXJ2YW5nZW5kZSBTaW1rYWFydDwvYT48L2xpPjxsaT48YSBpZD0iY29udGFjdCIgaHJlZj0iL2NvbnRhY3QuYXNweCI%2Bc3RlbCBqZSB2cmFhZzwvYT48L2xpPjwvdWw%2BZAIHD2QWCAIBDxYCHwAFCjA2MTIyNDAyNjlkAgMPFgIfAAUKMDYxMjI0MDI2OWQCBQ9kFgICAQ8QZBAVAhNTZWxlY3RlZXIgZWVuIG1hYW5kDW5vdmVtYmVyIDIwMTUVAgItMQYyMDE1MTEUKwMCZ2cWAWZkAgkPZBYCAgcPEGQPFgNmAgECAhYDEAUMRGF0dW0gLyB0aWpkBQExZxAFDkdla296ZW4gbnVtbWVyBQEyZxAFEUdlYnJ1aWtzY2F0ZWdvcmllBQEzZxYBZmRkK3p%2BtYxkLvddgufqkqVxIP01m5g%3D&__VIEWSTATEGENERATOR=84D43ECC&__EVENTVALIDATION=%2FwEWBQL5lNPqDgKEn6DQCwKH8Ma9BwLM44W%2BAgLHjbmPC%2F1Q14L24DXioQ3MR5DUBxqlRhmv&ctl00%24content%24ddlPeriod=-1';

    $this->doRequest('POST', $this->url . '/logout.aspx', $body);
  }
}

header('Content-type: application/json');

if (isset($_GET['username']) && isset($_GET['password']))
{
  $parser = new SimpelNLParser();
  $parser->doLogin($_GET['username'], $_GET['password']);
  $usage = $parser->getUsage();
  $parser->doLogout();

  print json_encode($usage);
}
else {
  print json_encode(array('error' => 'username or password missing'));
}