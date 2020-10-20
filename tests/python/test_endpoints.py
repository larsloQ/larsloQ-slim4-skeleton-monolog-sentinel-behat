#endpoints.py


import requests
import json
import pickle

#######################################################
## SETUP ##############################################
#######################################################
# set this to your env
baseurl = "http://larslo.larslo/slim4/public/"

# set username / password below (2x times)
#######################################################


#  functions need to start with test_
def test_hi():
    url= baseurl+"hi"
    headers = {'Content-Type': 'application/json' } 
    resp = requests.get(url, headers=headers)       
    assert resp.status_code == 200
    resp_body = resp.json()
    assert resp_body["say"] == "hey"

def checkEqual(L1, L2):
    return len(L1) == len(L2) and sorted(L1) == sorted(L2)


def test_texts():  
    url= baseurl+"texts"
    headers = {'Content-Type': 'application/json' } 
    resp = requests.get(url, headers=headers)       
    assert resp.status_code == 200
    r = json.loads(resp.json())
    assert len(r) == 7


def test_auth():  
    url= baseurl+"auth"
    headers = {'Content-Type': 'application/json' } 
    payload = json.dumps({
        'username': 'email',   # change to valid username / password
        'password': 'password' # change to valid username / password
    })     
    resp = requests.post(url, headers=headers, data=payload)       
    assert resp.status_code == 200
    r = resp.json()
    assert len(r) == 8
    for k in ['id','email','last_login']:
         assert k in r.keys()
    c = resp.cookies
    assert 'cartalyst_sentinel' in c


def get_auth_cookie():
    url= baseurl+"auth"
    headers = {'Content-Type': 'application/json' } 
    payload = json.dumps({
        'username': 'email',        # change to valid username / password
        'password': 'pw'            # change to valid username / password
    })
    resp = requests.post(url, headers=headers, data=payload)       
    c = resp.cookies
    assert resp.status_code == 200
    return c


def test_with_correct_authcookie():
    url= baseurl+"backend/current"
    headers = {'Content-Type': 'application/json' } 
    cookies=get_auth_cookie()
    resp = requests.get(url, headers=headers, cookies=cookies)       
    assert resp.status_code == 200



def test_with_wrong_authcookie():
    url= baseurl+"backend/current"
    headers = {'Content-Type': 'application/json' } 
    cookies = get_auth_cookie()
    # construct new cookies based on the once given
    # but alter the relevant value
    jar = requests.cookies.RequestsCookieJar()
    for c in cookies:      
        jar.set(c.name, "some-altered-value-NO_Underline-or-native-session-fails", domain=c.domain, path=c.path, expires=c.expires) 
    resp = requests.get(url, headers=headers, cookies=jar)
    assert resp.status_code == 403

    
   
# test_with_wrong_authcookie()
