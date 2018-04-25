# Install

- cd /opt && git clone https://github.com/codet-app/codet-server.git
- cd /opt/codet-server && composer install
- cp /opt/codet-server/.env.example /opt/codet-server/.env
- cp /opt/codet-server/codet.service /etc/systemd/system/codet.service
- systemctl enable codet.service
- systemctl start codet.service
