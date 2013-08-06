#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <time.h>
#include <pthread.h>
#include "dbg.h"

void *handle_conn(void *param)
{
    int *connfd = (int *) param;
    time_t ticks;
    char sendBuff[1025];

    ticks = time(NULL);

    check(snprintf(sendBuff, sizeof(sendBuff), "%.24s\r\n", ctime(&ticks)) > 0, "snprintf");
    check(write(*connfd, sendBuff, strlen(sendBuff)) != -1, "write");

    sleep(20);

    close(*connfd);
    free(param);

    return NULL;

error:
    if (connfd && *connfd)
        close(*connfd);
    if (param)
        free(param);
    return NULL;
}

int main(int argc, char *argv[])
{
    int listenfd = 0;
    struct sockaddr_in serv_addr;

    pthread_t             thread;
    int                   rc=0;
    void                 *status;

    listenfd = socket(AF_INET, SOCK_STREAM, 0);
    check(listenfd != -1, "create socket");

    serv_addr.sin_family = AF_INET;
    serv_addr.sin_addr.s_addr = htonl(INADDR_ANY);
    serv_addr.sin_port = htons(5000);

    check(bind(listenfd, (struct sockaddr*)&serv_addr, sizeof(serv_addr)) != -1, "bind");
    check(listen(listenfd, 10) != -1, "listen");

    printf("waiting...\n");

    while(1)
    {
        int *connfd = malloc(sizeof(int));
        check_mem(connfd);

        *connfd = accept(listenfd, (struct sockaddr*)NULL, NULL);
        check(*connfd != -1, "accept");
        rc = pthread_create(&thread, NULL, handle_conn, (void *) connfd);
        check(rc == 0, "pthread_create");

        printf("accepted\n");
    }

error:
    return 1;
}
