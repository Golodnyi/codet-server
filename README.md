# Install

- cd /opt && git clone https://github.com/codet-app/codet-server.git
- cp /opt/codet-server/codet.service /etc/systemd/system/codet.service
- systemctl enable codet.service
- systemctl start codet.service
