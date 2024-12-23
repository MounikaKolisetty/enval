import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = environment.apiUrl; // Replace with your backend URL 
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
  getUserCourses(): Observable<any> {
     return this.http.get(`${this.apiUrl}/course/user-courses`, { withCredentials: true }); 
  }
  sendHomeContact(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/sendEmail.php`, formData ,{ withCredentials: true });
  }
  sendUserContact(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/contactForm.php`, formData ,{ withCredentials: true });
  }
  sendTrainingForm(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/trainingForm.php`, formData ,{ withCredentials: true });
  }
  sendAdvisorForm(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/advisorForm.php`, formData ,{ withCredentials: true });
  }
  createOrder(amount: number): Observable<any> { 
    return this.http.post<any>(`${this.apiUrl}/create_order.php`, { amount }, { responseType: 'json' }); 
  } 
  verifyPayment(verifyDetails: any): Observable<any> { 
    return this.http.post(`${this.apiUrl}/verify_payment.php`, verifyDetails, { responseType: 'json' }); 
  }
}
