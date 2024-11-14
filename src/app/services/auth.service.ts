import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private nameSource = new BehaviorSubject<string>("");
  currentNameSource = this.nameSource.asObservable();

  private isLoggedInSource = new BehaviorSubject<boolean>(false);
  currentisLoggedIn = this.isLoggedInSource.asObservable();

  constructor() {}

  changeName(message:string) {
    this.nameSource.next(message);
  }
  changeIsLoggedin(info:boolean) {
    this.isLoggedInSource.next(info);
  }
}
