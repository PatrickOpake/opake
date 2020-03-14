## Steps for a new developer to install an Opake instance using Vagrant

1. Install Vagrant v. >= 1.8.5 from the official website: https://www.vagrantup.com/downloads.html
2. Go into project dir and run ```vagrant up```
3. Wait until creating a VM and provisioning is done
4. Add opake hosts to your local hosts file:
```
echo "33.33.33.33 opake.local patients.opake.local api.opake.local" >> /etc/hosts
```
5. Open http://opake.local in web-browser.