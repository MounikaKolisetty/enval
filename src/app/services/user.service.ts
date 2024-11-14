import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { catchError } from 'rxjs/operators'; 
import { throwError, of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = 'http://localhost:5062/api'; // Replace with your backend URL 
  constructor(private http: HttpClient) {} 
  signUp(user: any): Observable<any> { 
    return this.http.post(`${this.apiUrl}/user/signup`, user); 
  }
  login(email: string, password: string): Observable<any> { 
    return this.http.post(`${this.apiUrl}/user/login`, { email, password } ,{ withCredentials: true });
  }
  requestPasswordReset(email: string): Observable<any> {
     return this.http.post(`${this.apiUrl}/passwordreset/request`, { email }); 
  } 
  resetPassword(token: string, newPassword: string): Observable<any> {
     return this.http.post(`${this.apiUrl}/passwordreset/reset`, { token, newPassword }); 
  }
  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/user/logout`, {} ,{ withCredentials: true }); 
  }
}
