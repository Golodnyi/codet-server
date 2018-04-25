# Install

- cd /opt && git clone https://github.com/codet-app/codet-server.git
- cd /opt/codet-server && composer install
- cp /opt/codet-server/.env.example /opt/codet-server/.env
- sudo ln -s /opt/codet-server/codet.service /etc/systemd/system/codet.service
- sudo systemctl enable codet.service
- sudo systemctl start codet.service
