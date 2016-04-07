FROM tutum/lamp:latest

# Make sure everything is up-to-date
RUN sudo apt-get update

# Enable mcrypt support
RUN php5enmod mcrypt

RUN sudo apt-get -y install php5-curl

# Remove existing app folder and mount this folder as app root
RUN rm -fr /app
COPY . /app

# Add apache.conf
COPY /docs/churchis_apache.conf /etc/apache2/conf-enabled/

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php5/apache2/php.ini
RUN echo 'date.timezone = America/New_York' >> /etc/php5/apache2/php.ini

EXPOSE 80
CMD ["/run.sh"]
