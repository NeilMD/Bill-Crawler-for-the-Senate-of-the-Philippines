from bs4 import BeautifulSoup
import shutil
import requests
import os

for x in range(0, 2):
    base = "http://senate.gov.ph/lis/leg_sys.aspx?congress=17&type=bill&p="

    url = requests.get(base+str(x))
    source_code = url.text
    soup = BeautifulSoup(source_code, 'html.parser')
    linkContainer = soup.find('div',class_='alight')
    links = soup.find('div',class_='alight').find_all('a')
    for link in links:
        linkAc = link['href']
        base2 = 'http://senate.gov.ph/lis/'
        url = requests.get(base2 + linkAc)
        source_code = url.text
        soup = BeautifulSoup(source_code, 'html.parser')
        congressNum = soup.find('td', attrs={"id":"content"}).contents[0].strip()
        text = soup.find('td', attrs={"id":"content"})
        # 4 indicates 4th text, [9:] to remove Filed on
        removeFiledOn = text.contents[4][9:].split()
        date = removeFiledOn.pop(0)+ ' ' + removeFiledOn.pop(0) + ' ' + removeFiledOn.pop(0)
        #remove BY
        removeFiledOn.pop(0)
        fullname = ''
        for name in removeFiledOn:
            fullname = fullname + name + " "


        print(congressNum + " | " + date + " | " + fullname)



    # print(links)
