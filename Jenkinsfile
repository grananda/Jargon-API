pipeline {
  agent any
  stages {
    stage('Build dependencies') {
      steps {
        sh 'composer install'
      }
    }
    stage('Linters') {
      steps {
        sh 'composer cs:check'
      }
    }
    stage('Setting enviroment') {
      steps {
        sh '''cp .env.ci .env
'''
      }
    }
    stage('Test unit') {
      parallel {
        stage('Test unit') {
          steps {
            sh 'composer test:unit'
          }
        }
        stage('Test API') {
          steps {
            sh 'composer test:api'
          }
        }
        stage('Test external-service') {
          steps {
            sh 'composer test:external-service'
          }
        }
      }
    }
    stage('Deploy to staging') {
      agent {
        dockerfile {
          filename 'Dockerfile'
        }

      }
      steps {
        sh 'composer deploy:staging'
      }
    }
  }
}