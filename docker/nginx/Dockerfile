FROM nginx:latest

# Copy the custom Nginx configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# copy generated frontend to nginx
COPY ./public /var/www/html/public

# Fix permissions
RUN chmod -R 755 /var/www/html/public

# Expose port
EXPOSE 443
